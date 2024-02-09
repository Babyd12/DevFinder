<?php

namespace App\Entity;

use App\Entity\Apprenant;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\State\SetUserToRelationClass;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\EntrepriseRepository;
use App\State\AddUserToRelationProcessor;
use Doctrine\Common\Collections\Collection;
use App\State\RemoveUserToRelationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]

#[ApiResource(
    shortName: 'Module gestion de recrutement -Entreprise',
    normalizationContext: ['entreprise:recruter'],
    denormalizationContext: ['entreprise:recruter'],
    outputFormats: ['json' => 'application/json'],
    
    operations: [
        new Post(
            requirements: ['id' => '\d+' ],
            uriTemplate: 'entreprise/recruter/apprenant/{id}',
            processor: AddUserToRelationProcessor::class,
            security: "is_granted('ROLE_ENTREPRISE') or  'ROLE_ENTREPRISE' in user.getRoles()",
        ),
        new Post(
            requirements: ['id' => '\d+'],
            uriTemplate: 'entreprise/congedier/apprenant/{id}',
            processor: RemoveUserToRelationProcessor::class,
            security: "is_granted('ROLE_ENTREPRISE') or  'ROLE_ENTREPRISE' in user.getRoles()",
        ),
    ]
)]

#[GetCollection(
    shortName: 'Module gestion de compte -Entreprise',
    uriTemplate: 'entreprise/liste',
    description: 'Modifie toi',
    name: 'nom temporaire',
    normalizationContext: ['groups' => ['entreprise:index']],
    
)]

#[Get(
    shortName: 'Module gestion de compte -Entreprise',
    uriTemplate: 'entreprise/{id}',
    forceEager: true,
    normalizationContext: ['groups' => ['entreprise:show']]

)]

#[Post(
    shortName: 'Module gestion de compte -Entreprise',
    uriTemplate: 'entreprise/inscription',
    denormalizationContext: ['groups' => ['entreprise:create']]
)]

#[Put(
    shortName: 'Module gestion de compte -Entreprise',
    uriTemplate: 'entreprise/{id}',
    denormalizationContext: ['groups' => ['entreprise:update']]
)]

#[Patch(
    shortName: 'Module gestion de compte -Entreprise',
    uriTemplate: 'entreprise/change_mot_de_passe/{id}',
    denormalizationContext: ['groups' => ['entreprise:updateOne']]
)]

#[Delete(
    shortName: 'Module gestion de compte -Entreprise',
    uriTemplate: 'entreprise/{id}',
)]

class Entreprise implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['entreprise:show', 'entreprise:index', 'entreprise:update', 'entreprise:recruter'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 25, minMessage: 'veuillez saisir au moins 3 lettres', maxMessage: 'veuillez saisir moins de 20 lettres')]
    #[Assert\Type(type: 'string', message: 'La valeur {{ value }} doit être de type {{ type }}.')]
    #[Groups(['entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:update'])]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255,  unique: true, type: 'string')]
    #[Assert\Email(message: 'Veuillez entrer un format d\'email correcte.')]
    #[Groups(['entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:update'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Assert\PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_WEAK,
    ],  message: 'La force du mot de passe est trop faible. Veuillez utiliser un mot de passe plus fort')]
    #[Groups(['entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:updateOne'])]
    private ?string $mot_de_passe = null;

    #[ORM\ManyToMany(targetEntity: Apprenant::class, inversedBy: 'entreprises')]
    #[Groups(['entreprise:show',])]
    private Collection $apprenants;

    #[ORM\Column(length: 255,  unique: true, type: 'string')]
    #[Assert\Regex('/^7[7\-8\-6\-0\-5]+[0-9]{7}$/', message: 'Veuillez entre un format de numéro valide (Sénégal uniquement) ')]
    #[Groups(['entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:update'])]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Assert\NotBlank] 
    #[Assert\Length(min: 35, max: 250, minMessage: 'Veuillez saisir au minimum 35 caractères', maxMessage: 'Veuillez saisir moins 250 caractères',)]
    #[Assert\Regex('/^[a-zA-Z0-9À-ÿ\'\s]*$/', message: 'Le format du texte saisi est incorrecte.  ')]
    #[Groups(['entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:update'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex('/^\d{7} [0-9A-Z]{3}$/', message: 'Le format du NINEA est incorrecte. Exemple: sept chiffres puis le cofi 0001462 2G3')]
    #[Groups(['entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:update'])]
    private ?string $numero_identification_naitonal = null;

    #[ORM\Column]
    private ?bool $etat = null;

    public function __construct()
    {
        $this->apprenants = new ArrayCollection();
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
        $roles[] = 'ROLE_ENTREPRISE';
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
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): static
    {
        $this->apprenants->removeElement($apprenant);

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
}
