<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Trait\CommonDateTrait;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ImmersionRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Patch;
use App\Controller\CustomImmersionController;
use Symfony\Component\Serializer\Annotation\Groups;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints\Hostname;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImmersionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiResource(
    shortName: 'Module gestion de publication immersion -Administrateur',
    operations: [

        new Post(
            // shortName: 'Module Gestion de Publication de Projet - Association',
            uriTemplate: '/immersion/editer/cachier_charge/{id}',
            securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') ",
            securityMessage: "Vous n'avez pas l'autrorisaton requise",
            denormalizationContext: ['groups' => ['immersion:updateFile']],
            inputFormats: ['multipart' => 'multipart/form-data',],
            controller: CustomImmersionController::class,
            name: 'app_immersion_editer',
        ),
    ]
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

// #[Patch(
//     uriTemplate: 'immersion/{id}',
//     securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
//     denormalizationContext: ['groups' => ['immersion:update']],
//     inputFormats: ['json' => 'application/json'],
// )]

#[Delete(
    uriTemplate: 'immersion/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
)]

/**
 * je dois ajouter un processorr pour valider manuellemnt les données
 */
class Immersion
{
    use CommonDateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['immersion:show', 'immersion:index'])]
    private ?int $id = null;
    
    #[Vich\UploadableField(mapping: 'immersions', fileNameProperty: 'nomFichier', size: 'imageSize')]
    #[Assert\File(
        mimeTypes: [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
        mimeTypesMessage: 'Veuillez inserer un fichier de type pdf ou docx.'
    )]
    #[Assert\NotBlank(message: "Ce champs ne pas être vide", )]
    #[Groups(
        [
            'immersion:create', 'immersion:updateFile',
            /**
             * 
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * 
             * @see src/Entity/Apprenant
             */
            'apprenant:show'
        ], 
    )]  
    private ?File $cahierDeCharge = null;
    #[ORM\Column(nullable: true)]
    #[Groups(
        [
            'immersion:index', 'immersion:show',
            /**
             * @info Quand j'affiche un livrable qui a une relation avec une immersion jaffiche le pdf au lieu de luri
             */
            'livrable:show', 'livrable:index',
        ]
    )]  
    
    private ?string $nomFichier = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        "/^[a-zA-Z0-9À-ÿ]+(['.\-\s][a-zA-Z0-9À-ÿ]+)*[a-zA-Z0-9À-ÿ\s]*$/",
        message: "La valeur {{ value }}ne peut pas être vide ou composée uniquement d'espaces ou de caractères spéciaux"
    )]
    #[Assert\Length(
        min: 10,
        max: 250,
        minMessage: 'Votre titre doit comporter au moins {{ limit }} caractères',
        maxMessage: 'Votre titre ne peut pas dépasser {{ limit }} caractères'  
    )]
    #[Groups(
        [
            'immersion:show', 'immersion:index', 'immersion:create', 'immersion:update', 'immersion:updateFile',
            /**
             * @info Quand j'affiche un livrable qui a une relation avec une immersion jaffiche le pdf au lieu de luri
             */
            'livrable:show', 'livrable:index',
        ]
    )]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Url(
        message: 'L\'url {{ value }} n\'est pas une url valide',
    )]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:create', 'immersion:update', 'immersion:updateFile',])]
    private ?string $lien_support = null;

    #[ORM\OneToMany(mappedBy: 'immersion', targetEntity: Livrable::class)]
    #[Groups(['immersion:show', 'immersion:index'])]
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
    public function setCahierDecharge(?File $cahierDeCharge = null): void
    {
        $this->cahierDeCharge = $cahierDeCharge;

        if (null !== $cahierDeCharge) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            // $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getCahierDecharge(): ?File
    {
        return $this->cahierDeCharge;
    }

    public function setNomFichier(?string $nomFichier): void
    {
        $this->nomFichier = $nomFichier;
    }

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
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
