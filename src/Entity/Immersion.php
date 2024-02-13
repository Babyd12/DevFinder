<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ImmersionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Hostname;

#[ORM\Entity(repositoryClass: ImmersionRepository::class)]

#[ApiResource(
    shortName: 'Module gestion de publication immersion -Administrateur',
    outputFormats: [ 'json' => 'application/merge-patch+json', ]

)]


#[GetCollection(
    uriTemplate: 'immersion/liste',
    normalizationContext: ['groups' => ['immersion:index']],
)]

#[Get(
    uriTemplate: 'immersion/{id}',
    forceEager: true,
    normalizationContext: ['groups' => ['immersion:show']],
 

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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:update'])]
    private ?int $id = null;

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

    #[ORM\Column(length: 255)]
     #[Assert\NotBlank]
    #[Assert\Length(min: 35, max: 250, minMessage: 'Veuillez saisir au minimum 35 caractères', maxMessage: 'Veuillez saisir moins 250 caractères',)]
    #[Assert\Regex( pattern: '/[\d@*{}<>]+/', match: false, message: 'Le format de la description est incorrect')]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:create', 'immersion:update'])]
    private ?string $description = null;
    

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

}
