<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\State\SetUserToRelationClass;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ApprenantRepository;
use App\State\AddUserToRelationProcessor;
use App\State\GetApprenantProjetProcessor;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\SecurityBundle\Security;
use App\Controller\CustomApprenantController;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: ApprenantRepository::class)]

#[ApiResource(
    shortName: 'Module gestion de compte -Apprenant',
    description: "Cette API permet la gestion des comptes des apprenants. Elle offre des fonctionnalités telles que la création, la lecture, la mise à jour et la suppression de comptes apprenants. Les utilisateurs peuvent s'inscrire, se connecter, mettre à jour leurs informations de compte, etc.",

    operations: [
        new Patch(
            uriTemplate: 'apprenant/monitorerAccess/{id}',
            securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
            denormalizationContext: ['groups' => ['apprenant:monitorer']],
            normalizationContext: ['groups' => ['apprenant:monitorer']],
            inputFormats: ['json' => 'application/json'],
        ),
        // new Patch(

        //     uriTemplate: '/apprenant/competence/ajouter',
        //     // processor: AddUserToRelationProcessor::class,
        //     security: "is_granted('ROLE_ADMIN')",
        //     denormalizationContext: ['groups' => ['apprenant:ajouterCompetence']]
        // ),
        // new Delete(

        //     uriTemplate: '/apprenant/competence/supprimer/{id}',
        //     // processor: AddUserToRelationProcessor::class,
        //     security: "is_granted('ROLE_ADMIN')",
        //     denormalizationContext: ['groups' => ['apprenant:ajouterCompetence']]
        // )

    ],
)]

// Basic operations generated by api platform i have just litle custom it
#[GetCollection(
    uriTemplate: 'apprenant/liste',
    description: 'Modifie toi',
    normalizationContext: ['groups' => ['apprenant:index',]]
)]

#[Get(
    uriTemplate: 'apprenant/{id}',
    forceEager: true,
    normalizationContext: ['groups' => ['apprenant:show']],
    // outputFormats: ['json' => 'application/json']

)]

#[Post(
    uriTemplate: 'apprenant/inscription',
    name: 'créer un compte',
    denormalizationContext: ['groups' => ['apprenant:create']],

)]

#[Put(
    uriTemplate: 'apprenant/{id}',
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and (previous_object.getEmail() == user.getEmail() ) ",
    securityPostDenormalizeMessage: 'Vous n\'est pas apprenant ou le propriétaire de ce compte.',
    denormalizationContext: ['groups' => ['apprenant:update']],
    // normalizationContext: ['groups' => ['apprenant:update']],
)]

#[Patch(
    uriTemplate: 'apprenant/change_password/{id}',
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getUserIdentifier() == user.getUserIdentifier() ",
    // normalizationContext: ['groups' => ['apprenant:updateOne']],
    denormalizationContext: ['groups' => ['apprenant:updateOne']],
    formats: ['json' => 'application/json']
)]


#[Delete(
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getUserIdentifier() == user.getUserIdentifier() ",
    uriTemplate: 'apprenant/{id}',
)]

#[UniqueEntity(
    fields: 'email',
    message: 'Cet email existe déjà.',
)]
#[UniqueEntity(
    fields: 'telephone',
    message: 'Ce numero existe déjà.',
)]

class Apprenant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(
        [
            'apprenant:show', 'apprenant:index', 'apprenant:update', 'apprenant:monitorer',
            /**
             * ici lorsque jaffiche un projet qui a enregistré des apprenants, 
             * je charge les informations de l'apprenant au lieu de l'uri
             * @see App\Entity\Projet
             */
            'projet:show',
        ]
    )]
    private ?int $id = null;

    private ?int $id_getAble = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le champ ne doit pas être vide')]
    #[Assert\Length(min: 2, max: 25, minMessage: 'veuillez saisir au moins 3 lettres', maxMessage: 'veuillez saisir moins de 20 lettres')]
    #[Assert\Type(type: 'string', message: 'La valeur {{ value }} doit être de type {{ type }}.')]
    #[Groups(
        [
            'apprenant:show', 'apprenant:index', 'apprenant:create', 'apprenant:update',
            /**
             * ici lorsque jaffiche un projet qui a enregistré des apprenants, 
             * je charge les informations de l'apprenant au lieu de l'uri
             * @see App\Entity\Projet
             */
            'projet:show',
            /**
             * @info Quand j'affiche un livrable dans le quel un apprenant a fait une soumission 
             * j'affiche les informations de l'apprenant au lieu de l'uri
             * @see App\Entity\Projet
             * @return string
             */
            'livrable:index', 'livrable:show',
              /**
             * @see App\Entity\DescriptionCompetence
             * @return string
             */
            'descriptionCompetence:show',
            'descriptionCompetence:index',
        ]   
    )]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255,  unique: true, type: 'string')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9]{1,}([.]{0,1}[a-zA-Z0-9]+){1,26}@[a-z]+[.][a-z]{2,}$/',
        message: 'Veuillez entrer un format d\'email correcte.'
    )]
    #[Groups(
        [
            'apprenant:show', 'apprenant:index', 'apprenant:create', 'apprenant:update',
            /**
             * ici lorsque jaffiche un projet qui a enregistré des apprenants, 
             * je charge les informations de l'apprenant au lieu de l'uri
             * @see App\Entity\Projet
             * @return string
             */
            'projet:show',
             /**
             * @info Quand j'affiche un livrable dans le quel un apprenant a fait une soumission 
             * j'affiche les informations de l'apprenant au lieu de l'uri
             * @see App\Entity\Livrable
             * @return string
             */
            'livrable:index', 'livrable:show',
            /**
             * @see App\Entity\DescriptionCompetence
             * @return string
             */
            'descriptionCompetence:show',
            'descriptionCompetence:index',
        ]
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_WEAK,
    ],  message: 'La force du mot de passe est trop faible. Veuillez utiliser un mot de passe plus fort')]
    #[Groups(['apprenant:create', 'apprenant:updateOne'])]
    private ?string $mot_de_passe = null;

    #[ORM\Column]
    // #[Groups(['apprenant:index', 'apprenant:update', 'apprenant:updateOne'])]
    private array $roles = [];

    #[ORM\Column(type: Types::BINARY, nullable: true)]
    private $image = null;

    #[ORM\Column(length: 255, unique: true, type: 'string')]
    #[Assert\Regex('/^(70|78|77|76|75)\d{7}$/', message: 'Veuillez entre un format de numéro valide (Sénégal uniquement) ')]
    #[Groups(
        [
            'apprenant:create', 'apprenant:update', 'apprenant:show', 'apprenant:index',

        ]
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    #[Assert\Length(min: 35, max: 250, minMessage: 'Veuillez saisir au minimum 35 caractères', maxMessage: 'Veuillez saisir moins 250 caractères')]
    #[Assert\Regex(pattern: '/[\d@*{}<>]+/', match: false, message: 'Le format de la description est incorrect')]
    #[Groups(
        [
            'apprenant:create', 'apprenant:update', 'apprenant:show', 'apprenant:index',

        ]
    )]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Projet::class, inversedBy: 'apprenants')]
    #[Groups(['apprenant:show'])]
    private Collection $projet;

    #[ORM\ManyToMany(targetEntity: Entreprise::class, mappedBy: 'apprenants')]
    #[Groups(['apprenant:show'])]
    private Collection $entreprises;

    #[ORM\OneToMany(mappedBy: 'apprenant', targetEntity: Livrable::class)]
    #[Groups(['apprenant:show'])]
    private Collection $livrables;

    #[ORM\Column]
    #[Groups(
        [
            'apprenant:monitorer', 'apprenant:show', 'apprenant:index',
            /**
             * ici lorsque jaffiche un projet qui a enregistré des apprenants comme participants, 
             * je charge les informations de l'apprenant au lieu de l'uri
             * @see App\Entity\Projet
             */
            'projet:show',
        ]
    )]
    private ?bool $etat = null;

    #[ORM\OneToMany(mappedBy: 'apprenant', targetEntity: DescriptionCompetence::class, orphanRemoval: true)]
    #[Groups(
        [
            'apprenant:show', 'apprenant:index',
        ]
    )]
    private Collection $descriptionCompetences;

    #[ORM\OneToMany(mappedBy: 'apprenant', targetEntity: Message::class)]
    private Collection $messages;


    public function __construct()
    {

        $this->projet = new ArrayCollection();
        $this->entreprises = new ArrayCollection();
        $this->livrables = new ArrayCollection();
        $this->descriptionCompetences = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    /**
     * @see UserInterface
     *     
     */
    public function getPassword(): ?string
    {
        return $this->mot_de_passe;
    }
    public function setPassword(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGetable(): ?int
    {
        return $this->id_getAble;
    }
    public function setIdGetable(int $id_getAble): ?int
    {
        return $this->$id_getAble = $this->getId();
        return $this;
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

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }


    public function setMotDePasse(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }



    public function getRole(): string
    {
        $roles = $this->getRoles();
        return in_array('ROLE_APPRENANT', $roles) ? 'ROLE_APPRENANT' : null;
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
        $roles[] = 'ROLE_APPRENANT';

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

    /**
     * @return Collection<int, Projet>
     */
    public function getProjet(): Collection
    {
        return $this->projet;
    }

    public function addProjet(Projet $projet): static
    {
        if (!$this->projet->contains($projet)) {
            $this->projet->add($projet);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): static
    {
        $this->projet->removeElement($projet);

        return $this;
    }



    /**
     * @return Collection<int, Entreprise>
     */
    public function getEntreprises(): Collection
    {
        return $this->entreprises;
    }

    public function addEntreprise(Entreprise $entreprise): static
    {
        if (!$this->entreprises->contains($entreprise)) {
            $this->entreprises->add($entreprise);
            $entreprise->addApprenant($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): static
    {
        if ($this->entreprises->removeElement($entreprise)) {
            $entreprise->removeApprenant($this);
        }

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): static
    {
        $this->image = $image;

        return $this;
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
            $livrable->setApprenant($this);
        }

        return $this;
    }

    public function removeLivrable(Livrable $livrable): static
    {
        if ($this->livrables->removeElement($livrable)) {
            // set the owning side to null (unless already changed)
            if ($livrable->getApprenant() === $this) {
                $livrable->setApprenant(null);
            }
        }

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
     * @return Collection<int, DescriptionCompetence>
     */
    public function getDescriptionCompetences(): Collection
    {
        return $this->descriptionCompetences;
    }

    public function addDescriptionCompetence(DescriptionCompetence $descriptionCompetence): static
    {
        if (!$this->descriptionCompetences->contains($descriptionCompetence)) {
            $this->descriptionCompetences->add($descriptionCompetence);
            $descriptionCompetence->setApprenant($this);
        }

        return $this;
    }

    public function removeDescriptionCompetence(DescriptionCompetence $descriptionCompetence): static
    {
        if ($this->descriptionCompetences->removeElement($descriptionCompetence)) {
            // set the owning side to null (unless already changed)
            if ($descriptionCompetence->getApprenant() === $this) {
                $descriptionCompetence->setApprenant(null);
            }
        }

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
            $message->setApprenant($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getApprenant() === $this) {
                $message->setApprenant(null);
            }
        }

        return $this;
    }
}
