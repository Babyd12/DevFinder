<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BriefRepository;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Trait\CommonDateTrait;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\CustomBriefController;
use App\State\BriefStateProcessor;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints\Hostname;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BriefRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiResource(
    shortName: 'Module gestion de publication brief -Administrateur',
    operations: [
        new Post(
            // shortName: 'Module Gestion de Publication de Projet - Association',
            uriTemplate: '/brief/editer/cachier_charge/{id}',
            securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') ",
            securityMessage: "Vous n'avez pas l'autrorisaton requise",
            denormalizationContext: ['groups' => ['brief:updateFile']],
            inputFormats: ['multipart' => 'multipart/form-data',],
            controller: CustomBriefController::class,
            name: 'app_brief_editer',
        ),

    ]
)]

#[GetCollection(
    uriTemplate: 'brief/liste',
    forceEager: false,
    normalizationContext: ['groups' => ['brief:index']],
    // outputFormats: [ 'json' => 'application/json+ld'],
    // inputFormats: [ 'json' => 'application/json+ld; charset=utf-8']  
)]

#[Get(
    uriTemplate: 'brief/{id}',
    forceEager: true,
    normalizationContext: ['groups' => ['brief:show']],
    outputFormats: ['json' => 'application/json']

    // denormalizationContext: [ 'groups' => ['brief:show']],
)]

#[Post(
    uriTemplate: 'brief/publier',
    security: "is_granted('ROLE_ADMIN') ",
    // normalizationContext: [ 'groups' => ['brief:create']],
    denormalizationContext: ['groups' => ['brief:create']],
)]


#[Delete(
    uriTemplate: 'brief/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
)]

class Brief
{
    use CommonDateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['brief:show', 'brief:index', 'brief:modif'])]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'briefs', fileNameProperty: 'nomFichier', size: 'imageSize')]
    #[Assert\File(
        mimeTypes: [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
        mimeTypesMessage: 'Veuillez inserer un fichier de type pdf ou docx.'
    )]
    // #[Assert\Image(minWidth: 200, maxWidth: 400, minHeight: 200, maxHeight: 400)]
    #[Assert\NotBlank]
    #[Groups(
        [
            'brief:create', 'brief:updateFile',
        ]
    )]
    private ?File $cahierDeCharge = null;

    #[ORM\Column(nullable: true)]
    #[Groups(
        [
            'brief:index', 'brief:show',
        ]
    )]
    private ?string $nomFichier = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    #[Groups(
        [
            'brief:index', 'brief:show',
        ]
    )]
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
            'brief:show', 'brief:index', 'brief:create', 'brief:updateFile',
            /**
             * @info quand j'affiche un livrable qui a une relation avec une brief, j'affiche le titre au lieu de l'uri
             * @return string
             */
            'livrable:show', 'livrable:index',
        ]
    )]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Url(
        message: 'L\'url {{ value }} n\'est pas une url valide',
    )]
    #[Groups(
        [
            'brief:show', 'brief:create', 'brief:updateFile',
             /**
             * @info quand j'affiche un livrable qui a une relation avec une brief, j'affiche le titre au lieu de l'uri
             * @return string
             */
            'livrable:show', 'livrable:index',
        ]
    )]
    private ?string $lient_support = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(
        [
            'brief:show', 'brief:index', 'brief:create', 'brief:updateFile',
            /**
             * @info quand j'affiche un livrable qui a une relation avec une brief, j'affiche le titre au lieu de l'uri
             * @return string
             */
            'livrable:show', 'livrable:index',
        ]
    )]
    #[Assert\Regex(
        "/^(?!\s*$)(?![0-9]+$)(?![^a-zA-Z0-9À-ÿ\s]+$)[a-zA-Z0-9À-ÿ':\s]*$/", 
        message: 'Le format du texte saisi est incorrecte.')]
    #[Assert\Length(
        min: 10,
        max: 200,
        minMessage: 'Votre titre doit comporter au moins {{ limit }} caractères',
        maxMessage: 'Votre titre ne peut pas dépasser {{ limit }} caractères'  
    )]
    private ?string $niveau_de_competence = null;
    
    #[ORM\OneToMany(mappedBy: 'brief', targetEntity: Livrable::class)]
    #[Groups(['brief:show', 'brief:index'])]
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


    public function getLientSupport(): ?string
    {
        return $this->lient_support;
    }

    public function setLientSupport(string $lient_support): static
    {
        $this->lient_support = $lient_support;

        return $this;
    }

    public function getNiveauDeCompetence(): ?string
    {
        return $this->niveau_de_competence;
    }

    public function setNiveauDeCompetence(string $niveau_de_competence): static
    {
        $this->niveau_de_competence = $niveau_de_competence;

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
            $livrable->setBrief($this);
        }

        return $this;
    }

    public function removeLivrable(Livrable $livrable): static
    {
        if ($this->livrables->removeElement($livrable)) {
            // set the owning side to null (unless already changed)
            if ($livrable->getBrief() === $this) {
                $livrable->setBrief(null);
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
