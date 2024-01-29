<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
/*
#[ApiResource(
    shortName: 'Module gestion de compte -Association',
    
)]

#[GetCollection(
    uriTemplate: 'association/liste',
    normalizationContext: [ 'groups' => ['association:index'] ],
)]

#[Get(
    uriTemplate: 'association/{id}',
    forceEager: true,
    normalizationContext: [ 'groups' => ['association:show'] ]
)]

#[Post(
    security: "is_granted('ROLE_ASSOCIATION')",
    uriTemplate: 'association/inscription',
    denormalizationContext: [ 'groups' => ['association:create'] ]
)]

#[Put(
    uriTemplate: 'association/{id}',
    securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') and previous_object.getAssociation(user) == user ",
    denormalizationContext: [ 'groups' => ['association:update'] ],
)]

#[Patch(
    uriTemplate: '  association/change_password/{id}',
    securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') and previous_object.getAssociation(user) == user ",
    denormalizationContext: [ 'groups' => ['association:updateOne'] ]
)]

#[Delete(
    uriTemplate: 'association/{id}',
    securityPostDenormalize: "is_granted('ROLE_ASSOCIATION') and previous_object.getAssociation(user) == user ",
)]
*/
class Association implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Groups(['association:show', 'association:index', 'association:create', 'association:update'])]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255)]
    #[Groups(['association:show', 'association:index', 'association:create', 'association:update'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups([ 'association:create', 'association:update', 'association:updateOne'])]
    private ?string $mot_de_passe = null;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: Projet::class)]
    #[Groups([ 'association:show'])]
    private Collection $projets;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: LangageDeProgrammation::class)]
    #[Groups([ 'association:show'])]
    private Collection $langageDeProgrammations;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->langageDeProgrammations = new ArrayCollection();
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
}
