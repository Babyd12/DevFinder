<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Trait\CommonDateTrait;
use App\Repository\ImmersionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Hostname;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ImmersionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiResource(
    shortName: 'Module gestion de publication immersion -Administrateur',
    // outputFormats: [ 'json' => 'application/merge-patch+json' ],

)]


#[GetCollection(
    uriTemplate: 'immersion/liste',
    normalizationContext: ['groups' => ['immersion:index']],
    // outputFormats: [ 'json' => 'application/merge-patch+json' ]

)]

#[Get(
    uriTemplate: 'immersion/{id}',
    forceEager: true,
    normalizationContext: ['groups' => ['immersion:show']],
    // outputFormats: [ 'json' => 'application/merge-patch+json' ]

 

    // denormalizationContext: ['groups' => ['immersion:show']],
)]

#[Post(
    uriTemplate: 'immersion/publier',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
    // normalizationContext: ['groups' => ['immersion:create']],
    denormalizationContext: ['groups' => ['immersion:create']],
)]

#[Put(
    uriTemplate: 'immersion/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
    // normalizationContext: ['groups' => ['immersion:update']],
    denormalizationContext: ['groups' => ['immersion:update']]

)]

#[Delete(
    uriTemplate: 'immersion/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
)]

class Immersion
{
    use CommonDateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:update'])]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'immersions', fileNameProperty: 'imageName', size: 'imageSize')]
    #[Assert\File(
        mimeTypes: 
        [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
        mimeTypesMessage:'Veuillez inserer un fichier de type pdf ou docx.'
    )]
    #[Assert\NotBlank]
    #[Groups(
        [
            'immersion:create', 'immersion:index', 'immersion:create', 'immersion:update',
        ]
    )]
    private ?File $cahierDeCharge = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:create', 'immersion:update'])]
    private ?string $titre = null;
   
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Url(
        message: 'L\'url {{ value }} n\'est pas une url valide',
    )]
    #[Groups(['immersion:show','immersion:index', 'immersion:create', 'immersion:update'])]
    private ?string $lien_support = null;
        
    #[ORM\OneToMany(mappedBy: 'immersion', targetEntity: Livrable::class)]
    #[Groups(['immersion:show'])]
    private Collection $livrables;

   
    

    public function __construct()
    {
        $this->livrables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

   
    public function getLienSupport(): ?string
    {
        return $this->lien_support;
    }

    public function setLienSupport(string $lien_support): static
    {
        $this->lien_support = $lien_support;

        return $this;
    }


    /**
     * @return Collection<int, Livrable>
     */
    public function getLivrables(): Collection
    {
        return $this->livrables;
    }

    public function addLivrable(Livrable $livrable): static
    {
        if (!$this->livrables->contains($livrable)) {
            $this->livrables->add($livrable);
            $livrable->setImmersion($this);
        }

        return $this;
    }

    public function removeLivrable(Livrable $livrable): static
    {
        if ($this->livrables->removeElement($livrable)) {
            // set the owning side to null (unless already changed)
            if ($livrable->getImmersion() === $this) {
                $livrable->setImmersion(null);
            }
        }

        return $this;
    }


 /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $cahierDeCharge
     */
    public function setImageFile(?File $cahierDeCharge = null): void
    {
        $this->cahierDeCharge = $cahierDeCharge;

        if (null !== $cahierDeCharge) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            // $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->cahierDeCharge;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

}
