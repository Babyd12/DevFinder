<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\State\SetUserToRelationClass;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ApprenantRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\SecurityBundle\Security;
use App\Controller\CustomApprenantController;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: ApprenantRepository::class)]

#[ApiResource(
    shortName: 'Module gestion de compte -Apprenant',
    description: "Cette API permet la gestion des comptes des apprenants. Elle offre des fonctionnalités telles que la création, la lecture, la mise à jour et la suppression de comptes apprenants. Les utilisateurs peuvent s'inscrire, se connecter, mettre à jour leurs informations de compte, etc.",

    // operations: [
    //     new Get(
    //         requirements: ['id' => '\d+'],
    //         shortName: 'Module gestion de recrutement -Entreprise',
    //         uriTemplate: 'entreprise/recruter/apprenant/{id}',
    //     security: "is_granted('ROLE_APPRENANT')",
    //         processor: SetUserToRelationClass::class,
    //         denormalizationContext: ['entreprise:recruter'],
    //         normalizationContext: ['entreprise:recruter'],
    //     ),
    // ]
)]

// Basic operations generated by api platform i have just litle custom it
#[GetCollection(
    uriTemplate: 'apprenant/liste',
    description: 'Modifie toi',
    name: 'nom temporaire',
    normalizationContext: ['groups' => ['apprenant:index']]
)]

#[Get(
    uriTemplate: 'apprenant/{id}',
    forceEager: true,
    normalizationContext: ['groups' => ['apprenant:show']]
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
    normalizationContext: ['groups' => ['apprenant:update']],
)]

#[Patch(
    uriTemplate: 'apprenant/change_password/{id}',
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getUserIdentifier() == user.getUserIdentifier() ",
    normalizationContext: ['groups' => ['apprenant:updateOne']],
    denormalizationContext: ['groups' => ['apprenant:updateOne']],
    formats: ['json' => 'application/json']
)]

#[Delete(
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getUserIdentifier() == user ",
    uriTemplate: 'apprenant/{id}',
)]

class Apprenant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['apprenant:show', 'apprenant:index', 'apprenant:create', 'apprenant:update'])]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255)]
    #[Groups(['apprenant:show', 'apprenant:index', 'apprenant:create', 'apprenant:update'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['apprenant:create', 'apprenant:update', 'apprenant:updateOne'])]
    private ?string $mot_de_passe = null;

    #[ORM\Column]
    // #[Groups(['apprenant:index', 'apprenant:update', 'apprenant:updateOne'])]
    private array $roles = [];

    #[ORM\ManyToOne(targetEntity: Immersion::class, inversedBy: 'apprenants')]
    #[Groups(['apprenant:show'])]
    private ?Immersion $immersion = null;

    #[ORM\ManyToMany(targetEntity: Brief::class, mappedBy: 'apprenant')]
    #[Groups(['apprenant:show'])]
    private Collection $briefs;

    #[ORM\ManyToMany(targetEntity: Projet::class, inversedBy: 'apprenants')]
    #[Groups(['apprenant:show'])]
    private Collection $projet;

    #[ORM\OneToMany(mappedBy: 'apprenant', targetEntity: Competence::class)]
    #[Groups(['apprenant:show'])]
    private Collection $competences;

    #[ORM\ManyToMany(targetEntity: Entreprise::class, mappedBy: 'apprenants')]
    private Collection $entreprises;

    #[ORM\Column(type: Types::BINARY, nullable: true)]
    private $image = null;


    public function __construct()
    {
        $this->briefs = new ArrayCollection();
        $this->projet = new ArrayCollection();
        $this->competences = new ArrayCollection();
        $this->entreprises = new ArrayCollection();
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

    public function getImmersion(): ?Immersion
    {
        return $this->immersion;
    }

    public function setImmersion(?Immersion $immersion): static
    {
        $this->immersion = $immersion;

        return $this;
    }

    /**
     * @return Collection<int, Brief>
     */
    public function getBriefs(): Collection
    {
        return $this->briefs;
    }

    public function addBrief(Brief $brief): static
    {
        if (!$this->briefs->contains($brief)) {
            $this->briefs->add($brief);
            $brief->addApprenant($this);
        }

        return $this;
    }

    public function removeBrief(Brief $brief): static
    {
        if ($this->briefs->removeElement($brief)) {
            $brief->removeApprenant($this);
        }

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
     * @return Collection<int, Competence>
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competence $competence): static
    {
        if (!$this->competences->contains($competence)) {
            $this->competences->add($competence);
            $competence->setApprenant($this);
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): static
    {
        if ($this->competences->removeElement($competence)) {
            // set the owning side to null (unless already changed)
            if ($competence->getApprenant() === $this) {
                $competence->setApprenant(null);
            }
        }

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

   
}
