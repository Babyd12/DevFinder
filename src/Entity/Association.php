<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]

#[ApiResource(
    shortName: 'Module gestion de compte -Association',
    operations: [
        new Patch(
            uriTemplate: 'association/monitorerAccess/{id}',
            securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
            denormalizationContext: ['groups' => ['administrateur:monitorer']],
            // normalizationContext: [ 'groups' => ['administrateur:monitorer'] ],
        ),
        // new Patch(
        //     uriTemplate:'association/{id}'
        // )
        // new Patch(
        //     uriTemplate:'association/test',
        // )
    ],
)]

#[GetCollection(
    uriTemplate: 'association/liste',
    normalizationContext: ['groups' => ['association:index']],

)]

#[Get(
    uriTemplate: 'association/{id}',
    forceEager: true,
    normalizationContext: ['groups' => ['association:show']]
)]

#[Post(
    uriTemplate: 'association/inscription',
    denormalizationContext: ['groups' => ['association:create']]
)]

#[Put(
    uriTemplate: 'association/{id}',
    securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') and previous_object.getUserIdentifier() == user.getUserIdentifier()  ",
    denormalizationContext: ['groups' => ['association:update']],
)]

// #[Patch(
//     uriTemplate: 'association/change_password/{id}',
//     securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') and previous_object.getUserIdentifier() == user.getUserIdentifier()  ",
//     // denormalizationContext: ['groups' => ['association:updateOne']],
//     inputFormats: ['json' => ['application/json']],
// )]
#[Patch(
    uriTemplate: 'association/updateOne/{id}',
    securityPostDenormalize: "is_grandted('ROLE_ASSOCIATION')",
    denormalizationContext: ['groups'=>['association:update_one']],
    inputFormats: ['json' => ['application/json']],
)]

#[Delete(
    uriTemplate: 'association/{id}',
    securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') and previous_object.getUserIdentifier() == user.getUserIdentifier()  ",
)]

class Association implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('association:index', 'association:show')]
    private ?int $id = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide')]
    #[Assert\Length(min: 2, max: 20, minMessage: 'veuillez saisir au moins 3 lettres', maxMessage: 'veuillez saisir moins de 20 lettres')]
    #[Assert\Type(type: 'string', message: 'La valeur {{ value }} doit être de type {{ type }}.')]
    #[Groups(
        [
            'association:show', 'association:index', 'association:create', 'association:update',
            /**
             * @see App\Entity/Projet
             */
            'projet:show',

            /**
             * @see App\Entity/Apprenant
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations de lassociation au lieu de l'uri
             */
            'apprenant:show'
        ]
    )]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255,  unique: true, type: 'string')]
    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9]{1,}([._]{0,1}[a-zA-Z0-9]+){1,26}@[a-z]+[.][a-z]{2,}$/',
        message: 'Veuillez entrer un format d\'email correcte. {{ value }}'
    )]
    #[Groups(
        [
            'association:show', 'association:index', 'association:create', 'association:update',
            /**
             * @see App\Entity/Apprenant
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations de lassociation au lieu de l'uri
             */
            'apprenant:show'
        ]
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true, type: 'string')]
    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide')]
    #[Assert\Regex('/^7[7\-8\-6\-0\-5]+[0-9]{7}$/', message: '{{ value }}Veuillez entre un format de numéro valide (Sénégal uniquement) ')]
    #[Groups(['association:show', 'association:index', 'association:create', 'association:update'])]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide')]
    #[Assert\Length(min: 35, max: 200, minMessage: 'Veuillez saisir au minimum 35 caractères', maxMessage: 'Veuillez saisir moins 250 caractères',)]
    #[Assert\Regex(pattern: '/[\d@*{}<>]+/', match: false, message: 'Le format de la description est incorrect')]
    #[Groups(['association:show', 'association:index', 'association:create', 'association:update'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide')]
    #[Assert\Regex('/^\d{7} [0-9A-Z]{3}$/', message: 'Le format du NINEA est incorrecte. Exemple: sept chiffres puis le cofi 0001462 2G3')]
    #[Groups(['association:show', 'association:index', 'association:create'])]
    private ?string $numero_identification_naitonal = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ ne doit pas être vide")]
    #[Assert\PasswordStrength(
        [
            'minScore' => PasswordStrength::STRENGTH_WEAK,
        ],
        message: 'La force du mot de passe est trop faible. Veuillez utiliser un mot de passe plus fort'
    )]
    #[Groups(
        [
            'association:create', 
        ]
    )]
    private ?string $mot_de_passe = null;

    #[ORM\Column]
    #[Groups(['administrateur:monitorer', 'association:show', 'association:index'])]
    private ?bool $etat = null;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: Projet::class)]
    #[Groups(
        [
            'association:show',
        ]
    )]
    private Collection $projets;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: LangageDeProgrammation::class)]
    #[Groups(
        [
            'association:show',
        ]
    )]
    private Collection $langageDeProgrammations;


    #[ORM\OneToMany(mappedBy: 'association', targetEntity: Message::class)]
    private Collection $messages;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->langageDeProgrammations = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): ?string
    {
        return $this->nom_complet;
    }

    public function setNomComplet(string $nom_complet): static
    {
        $this->nom_complet = $nom_complet;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->mot_de_passe;
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    /**
     * @return Collection<int, Projet>
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): static
    {
        if (!$this->projets->contains($projet)) {
            $this->projets->add($projet);
            $projet->setAssociation($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): static
    {
        if ($this->projets->removeElement($projet)) {
            // set the owning side to null (unless already changed)
            if ($projet->getAssociation() === $this) {
                $projet->setAssociation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LangageDeProgrammation>
     */
    public function getLangageDeProgrammations(): Collection
    {
        return $this->langageDeProgrammations;
    }

    public function addLangageDeProgrammation(LangageDeProgrammation $langageDeProgrammation): static
    {
        if (!$this->langageDeProgrammations->contains($langageDeProgrammation)) {
            $this->langageDeProgrammations->add($langageDeProgrammation);
            $langageDeProgrammation->setAssociation($this);
        }

        return $this;
    }

    public function removeLangageDeProgrammation(LangageDeProgrammation $langageDeProgrammation): static
    {
        if ($this->langageDeProgrammations->removeElement($langageDeProgrammation)) {
            // set the owning side to null (unless already changed)
            if ($langageDeProgrammation->getAssociation() === $this) {
                $langageDeProgrammation->setAssociation(null);
            }
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_ASSOCIATION';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

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

    public function getNumeroIdentificationNaitonal(): ?string
    {
        return $this->numero_identification_naitonal;
    }

    public function setNumeroIdentificationNaitonal(string $numero_identification_naitonal): static
    {
        $this->numero_identification_naitonal = $numero_identification_naitonal;

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): static
    {
        $this->etat = $etat;

        return $this;
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
            $message->setAssociation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAssociation() === $this) {
                $message->setAssociation(null);
            }
        }

        return $this;
    }
}
