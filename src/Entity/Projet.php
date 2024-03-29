<?php

namespace App\Entity;

use App\Entity\Message;
use App\Entity\Apprenant;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use App\State\ProjetUpdateFile;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\State\ProjetStateProcessor;
use App\Repository\ProjetRepository;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Trait\CommonDateTrait;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\CustomProjetController;
use phpDocumentor\Reflection\DocBlock\Tag;
use App\State\ShowCollectionsStateProvider;
use Doctrine\Common\Collections\Collection;
use App\Controller\CustomApprenantController;
use App\State\GetUserAndHerRelationsProvider;
use App\State\ProjePostStateProcessor;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints\Hostname;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiResource(
    shortName: 'Module Gestion de participation Projet - Apprenant',
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
            shortName: 'Module Gestion de Publication de Projet - Association',
            uriTemplate: '/projet/{id}',
            securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') ",
            securityMessage: "Vous n'avez pas l'autrorisaton requise",
            denormalizationContext: ['groups' => ['projet:updateFile']],
            inputFormats: ['multipart' => 'multipart/form-data',],
            processor: ProjetStateProcessor::class,
            // name: 'app_projet_editer',
        ),

        new Post(
            shortName: 'Module Gestion de participation Projet - Apprenant',
            uriTemplate: '/apprenant/soumettre/livrableProjet/{id}',
            security: "is_granted('ROLE_APPRENANT') and object.apprenantIsInProjet(user) == true ",
            securityMessage: "Vous n'êtes pas apprenant ou ne faite pas partir de ce projet",
            denormalizationContext: ['groups' => ['projet:livrable']],
            inputFormats: ['multipart' => 'multipart/form-data',],
            processor: ProjetStateProcessor::class,
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
    // denormalizationContext: ['groups' => ['projet:index']],

)]

#[Get(
    forceEager: true,
    shortName: 'Module Gestion de Publication de Projet - Association',
    uriTemplate: '/projet/{id}',
    normalizationContext: ['groups' => ['projet:show']],

)]

#[Post(
    security: "is_granted('ROLE_ASSOCIATION')",
    shortName: 'Module Gestion de Publication de Projet - Association',
    uriTemplate: '/projet/ajouter',
    denormalizationContext: ['groups' => ['projet:create']],
)]


#[Delete(
    uriTemplate: '/projet/{id}',
    securityPostDenormalize: "is_granted('ROLE_ASSOCIATION')  ",
    shortName: 'Module Gestion de Publication de Projet - Association',
)]



class Projet
{
    use CommonDateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(
        [
            'projet:index', 'projet:show', 
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

    #[Vich\UploadableField(mapping: 'projets', fileNameProperty: 'nomFichier', size: 'imageSize')]
    #[Assert\File(
        mimeTypes: [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
        mimeTypesMessage: 'Veuillez inserer un fichier de type pdf ou docx.',
    )]
    // #[Assert\Image(minWidth: 200, maxWidth: 400, minHeight: 200, maxHeight: 400)]
    // #[Assert\NotBlank]
    #[Groups(
        [
            'projet:create', 'projet:updateFile', 
        ]
    )]
    private ?File $CahierDecharge = null;

    #[ORM\Column(nullable: true)]
    #[Groups(
        [
            'projet:index', 'projet:show',
            /**
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * @see src/Entity/Apprenant
             * 
             */
            'apprenant:show'
        ]
    )]
    private ?string $nomFichier = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    #[Groups(
        [
            'projet:index', 'projet:show',
        ]
    )]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\Type(
        type: 'string',
        message: 'La valeur {{ value }} nest pas un type {{ type }} valide.',
    )]
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
            'projet:show', 'projet:index', 'projet:create', 'projet:updateFile',
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

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Regex(
        pattern: '/^(?!\s*$)[1-9]$/',
        match: true,
        message: "La valeur de '{{ value }}' doit être un entier et compris entre 1 et 9.",
    )]

    #[Groups(
        [
            'projet:show', 'projet:index', 'projet:create', 'projet:updateFile',
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
    private ?string $nombre_de_participant = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]

    #[Assert\GreaterThan('today', message: 'Veuillez fournir une date correcte suppérieur à aujourd\'hui')]
    #[Assert\NotBlank(message: 'Ce champs ne dois pas être vide')]

    #[Groups(
        [
            'projet:show', 'projet:index', 'projet:create', 'projet:updateFile',
            /**
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * @see src/Entity/Apprenant
             */
            'apprenant:show'
        ]
    )]
    private ?\DateTimeInterface $date_limite = null;

    #[ORM\Column(length: 255, nullable: true)]
    // #[Assert\NotBlank()]
    // #[Assert\Regex('/^\S.*$/', message: "La valeur {{ value }} ne peut pas être vide ou composée uniquement d'espaces")]
    #[Assert\Url(message: 'L\'url {{ value }} n\'est pas une url valide')]
    #[Groups(
        [
            'projet:show', 'projet:index', 'projet:updateFile', 'projet:livrable',
            /**
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * @see src/Entity/Apprenant
             */
            'apprenant:show'
        ]
    )]
    private ?string $lien_du_repertoire_distant = null;

    #[ORM\ManyToOne(targetEntity: Association::class, inversedBy: 'projets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(
        [
            'projet:show', 'projet:show', 'projet:index',
            /**
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du projet au lieu de l'uri
             * @see src/Entity/Apprenant
             */
            'apprenant:show',
        ]
    )]
    private ?Association $association = null;


    #[ORM\ManyToMany(targetEntity: LangageDeProgrammation::class, inversedBy: 'projets')]
    #[Groups(['projet:create', 'projet:show', 'projet:index', 'projet:updateFile',])]
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
            'projet:show', 'projet:index', 'projet:create', 'projet:updateFile',
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
     *@param App\Entity\Association
     *@return boolean
     */
    public function isProjectOwner(Association $association): bool
    {
        return $this->association === $association;
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

   

    /**
     * @param string $apprenant
     * @return bool
     * @info this method checks if the apprenant is in 
     */
    public function apprenantIsInProjet(Apprenant $apprenantRecherche): bool
    {
        return $this->getApprenants()->contains($apprenantRecherche);
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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $CahierDecharge
     */
    public function setCahierDecharge(?File $CahierDecharge = null): void
    {
        $this->CahierDecharge = $CahierDecharge;

        if (null !== $CahierDecharge) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            // $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getCahierDecharge(): ?File
    {
        return $this->CahierDecharge;
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

    public function getNombreDeParticipant(): ?string
    {
        return $this->nombre_de_participant;
    }

    public function setNombreDeParticipant(?string $nombre_de_participant): static
    {
        $this->nombre_de_participant = $nombre_de_participant;

        return $this;
    }

    public function getLienDuRepertoireDistant(): ?string
    {
        return $this->lien_du_repertoire_distant;
    }

    public function setLienDuRepertoireDistant(?string $lien_du_repertoire_distant): static
    {
        $this->lien_du_repertoire_distant = $lien_du_repertoire_distant;

        return $this;
    }
}
