<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\State\AddUserToRelationProcessor;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\EntrepriseRepository;
use App\State\RemoveUserToRelationProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[ApiResource(
    shortName: 'Module gestion de recrutement -Entreprise',
    outputFormats: ['json' => 'application/json'],
    operations: [
        new Post(
            requirements: ['id' => '\d+'],
            uriTemplate: 'entreprise/recruter/apprenant/{id}',
            processor: AddUserToRelationProcessor::class,
            security: "is_granted('ROLE_ENTREPRISE') or  'ROLE_ENTREPRISE' in user.getRoles()",
            denormalizationContext: ['entreprise:recruter'],
            normalizationContext: ['entreprise:recruter'],
        ),
        new Post(
            requirements: ['id' => '\d+'],
            uriTemplate: 'entreprise/congedier/apprenant/{id}',
            processor: RemoveUserToRelationProcessor::class,
            security: "is_granted('ROLE_ENTREPRISE') or  'ROLE_ENTREPRISE' in user.getRoles()",
            denormalizationContext: ['entreprise:recruter'],
            normalizationContext: ['entreprise:recruter'],
        ),

        new Patch(
            uriTemplate: 'entreprise/monitorer/{id}',
            securityPostDenormalize: "is_granted('ROLE_ADMINISTRATEUR') ",
            denormalizationContext: [ 'groups' => ['association:monitorer'] ]
        )
    ]
)]


#[GetCollection(
    shortName: 'Module gestion de compte -Entreprise',
    uriTemplate: 'entreprise/liste',
    description: 'Modifie toi',
    name: 'nom temporaire',
    normalizationContext: ['groups' => ['entreprise:index']]
)]

#[Get(
    forceEager: true,
    shortName: 'Module gestion de compte -Entreprise',
    uriTemplate: 'entreprise/{id}',
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
    #[Groups(
        [
            'entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:update',
            // 'apprenant:show',
        ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(
        [
            'entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:update',
            // 'apprenant:show',
        ]
    )]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255)]
    #[Groups(
        [
            'entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:update',
            // 'apprenant:index',
        ]
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Groups(['entreprise:show', 'entreprise:index', 'entreprise:create', 'entreprise:update', 'apprenant:show'])]
    private ?string $mot_de_passe = null;

    #[ORM\ManyToMany(targetEntity: Apprenant::class, inversedBy: 'entreprises')]
    #[Groups(['entreprise:show'])]
    private Collection $apprenants;

    #[ORM\Column(options:['default',false])]
    #[Groups(['association:monitorer'])]
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
