<?php

namespace App\Entity;

use App\Entity\Apprenant;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjetRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\CustomProjetController;
use phpDocumentor\Reflection\DocBlock\Tag;
use App\State\ShowCollectionsStateProvider;
use Doctrine\Common\Collections\Collection;
use App\Controller\CustomApprenantController;
use App\Entity\Trait\CommonDateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Hostname;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiResource(
    shortName: 'Module Gestion de Participation -Projet',
    operations: [
        new Get(
            uriTemplate: '/apprenant/participer/projet/{id}',
            security: "is_granted('ROLE_APPRENANT')",
            routeName: 'participerProjet',
            controller: CustomProjetController::class,
            normalizationContext: ['groups' => 'apprenantPojet:show'],
            denormalizationContext: ['groups' => 'apprenant:participate'],
            securityMessage: 'Only authenticated users can access this resource.',
        ),
        new Get(
            uriTemplate: '/apprenant/quitter/projet/{id}',
            security: "is_granted('ROLE_APPRENANT')",
            routeName: 'quitterProjet',
            controller: CustomProjetController::class,
            normalizationContext: ['groups' => 'apprenantQuitterPojet:show'],
            denormalizationContext: ['groups' => 'apprenantQuitterProjet:create'],
        ),
        new Post(
            uriTemplate: 'projet/ajouter/v2',
        )
    ]
)]

#[GetCollection(
    hydraContext: ['groups' => 'apprenantQuitterProjet'],
    shortName: 'Module Gestion de Publication de Projet - Association',
    uriTemplate: '/projet/liste',

    description: 'Affiche tout les projet',
    name: 'un nom simple a comprndre',
    normalizationContext: ['groups' => ['projet:index']],
    denormalizationContext: ['groups' => ['projet:index']],

)]

#[Get(
    forceEager: true,
    shortName: 'Module Gestion de Publication de Projet - Association',
    uriTemplate: '/projet/{id}',
    normalizationContext: ['groups' => ['projet:show']]
)]

#[Post(
    security: "is_granted('ROLE_ASSOCIATION')",
    shortName: 'Module Gestion de Publication de Projet - Association',
    uriTemplate: '/projet/ajouter',
    denormalizationContext: ['groups' => ['projet:create']]
)]

#[Put(
    shortName: 'Module Gestion de Publication de Projet - Association',
    uriTemplate: '/projet/{id}',
    securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') and previous_object.getAssociation(user) == user ",
    // securityMessage: 'Sorry, but you are not this projet owner.',
    denormalizationContext: ['groups' => ['projet:update']]
)]

#[Delete(
    uriTemplate: '/projet/{id}',
    securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') and previous_object.getAssociation(user) == user ",
    shortName: 'Module Gestion de Publication de Projet - Association',
)]

#[UniqueEntity(
    fields: ['titre', 'association'],
    errorPath: 'association',
    message: 'Cette association a déjà un projet portant ce titre',
)]

class Projet
{
    use CommonDateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(
        [
            'projet:show', 'projet:create', 'projet:update',
            /**
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * @see src/Entity/Apprenant
             * 
             */
            'apprenant:show'
        ]
    )]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'projets', fileNameProperty: 'imageName', size: 'imageSize')]
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
            'projet:create', 'projet:index', 'projet:create', 'projet:update',
        ]
    )]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(
        [
            'projet:show', 'projet:index', 'projet:create', 'projet:update',
            /**
             * 
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * 
             * @see src/Entity/Apprenant
             */
            'apprenant:show'
        ]
    )]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 35, max: 250, minMessage: 'Veuillez saisir au minimum 35 caractères', maxMessage: 'Veuillez saisir moins 250 caractères',)]
    #[Assert\Regex(pattern: '/[\d@*{}<>]+/', match: false, message: 'Le format de la description est incorrect')]
    #[Groups(
        [
            'projet:show', 'projet:index', 'projet:create', 'projet:update',
            /**
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * @see src/Entity/Apprenant
             */
            'apprenant:show'
        ]
    )]
    private ?string $description = null;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: 'Ce champs ne dois pas être vide')]
    #[Groups(
        [
            'projet:show', 'projet:index', 'projet:create', 'projet:update',

            /**
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * @see src/Entity/Apprenant
             */
            'apprenant:show'
        ]
    )]
    private ?int $nombre_de_participant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\GreaterThan('today')]
    #[Assert\NotBlank(message: 'Ce champs ne dois pas être vide')]
    #[Groups(
        [
            'projet:show', 'projet:index', 'projet:create', 'projet:update',
            /**
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * @see src/Entity/Apprenant
             */
            'apprenant:show'
        ]
    )]
    private ?\DateTimeInterface $date_limite = null;

    #[ORM\ManyToOne(targetEntity: Association::class, inversedBy: 'projets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(
        [
            'projet:show',
            /**
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * @see src/Entity/Apprenant
             */
            'apprenant:show',
        ]
    )]
    private ?Association $association = null;

    // #[ORM\Column(['string'])]
    #[ORM\ManyToMany(targetEntity: LangageDeProgrammation::class, inversedBy: 'projets')]
    #[Groups(['projet:create', 'projet:show'])]
    private Collection $langage_de_programmation;

    #[ORM\ManyToMany(targetEntity: Apprenant::class, mappedBy: 'projet')]
    #[Groups(
        [
            'projet:show',
        ]
    )]
    private Collection $apprenants;

    #[ORM\Column(type: "string", enumType: ProjetStatu::class)]
    #[Groups(
        [
            'projet:show', 'projet:index', 'projet:create', 'projet:update',
             /**
             * 
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * 
             * @see src/Entity/Apprenant
             */
            'apprenant:show'
        ]
    )]
    private ?ProjetStatu $statu = null;

    #[ORM\OneToMany(mappedBy: 'projet', targetEntity: Message::class)]
    private Collection $messages;



    
    public function __construct()
    {
        $this->langage_de_programmation = new ArrayCollection();
        $this->apprenants = new ArrayCollection();
        $this->messages = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getNombreDeParticipant(): ?int
    {
        return $this->nombre_de_participant;
    }

    public function setNombreDeParticipant(int $nombre_de_participant): static
    {
        $this->nombre_de_participant = $nombre_de_participant;

        return $this;
    }

    public function getDateLimite(): ?\DateTimeInterface
    {
        return $this->date_limite;
    }

    public function setDateLimite(\DateTimeInterface $date_limite): static
    {
        $this->date_limite = $date_limite;

        return $this;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): static
    {
        $this->association = $association;

        return $this;
    }

    /**
     * @return Collection<int, LangageDeProgrammation>
     */
    public function getLangageDeProgrammation(): Collection
    {
        return $this->langage_de_programmation;
    }

    public function addLangageDeProgrammation(LangageDeProgrammation $langageDeProgrammation): static
    {
        if (!$this->langage_de_programmation->contains($langageDeProgrammation)) {
            $this->langage_de_programmation->add($langageDeProgrammation);
        }

        return $this;
    }

    public function removeLangageDeProgrammation(LangageDeProgrammation $langageDeProgrammation): static
    {
        $this->langage_de_programmation->removeElement($langageDeProgrammation);

        return $this;
    }

    /**
     * @return Collection<int, Apprenant>
     */
    public function getApprenants(): Collection
    {
        return $this->apprenants;
    }

    public function addApprenant(Apprenant $apprenant): static
    {
        if (!$this->apprenants->contains($apprenant)) {
            $this->apprenants->add($apprenant);
            $apprenant->addProjet($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): static
    {
        if ($this->apprenants->removeElement($apprenant)) {
            $apprenant->removeProjet($this);
        }

        return $this;
    }

    public function getStatu(): ?ProjetStatu
    {
        return $this->statu;
    }

    public function setStatu(?ProjetStatu $statu): static
    {
        $this->statu = $statu;

        return $this;
    }

    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            // $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setProjet($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getProjet() === $this) {
                $message->setProjet(null);
            }
        }

        return $this;
    }

   

   
}
