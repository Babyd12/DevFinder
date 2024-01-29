<?php

namespace App\Entity;

use MessageFormatter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AdministrateurRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]

#[ApiResource(
    shortName: 'Module gestion de publication -Administrateur'
)]

#[GetCollection(
    uriTemplate: '/administrateur',
    normalizationContext: ['groups' => ['read']],
)]

#[Put(
    uriTemplate: '/administrateur/{id}',
    denormalizationContext: ['groups' => ['write:put']],
    requirements: ['id' => '\d+'],
    securityPostDenormalize: "is_granted('ROLE_ADMIN') and object.isUsedInProjects() == false",
)]

#[Patch(
    uriTemplate: '/administrateur/change_password{id}',
    denormalizationContext: ['groups' => ['write:patch']],
    formats: ['json' => 'application/json'],
)]



class Administrateur  implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'readAll', 'write', 'write:put'])]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'readAll', 'write', 'write:put'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['write', 'write:put', 'write:patch'])]
    private ?string $mot_de_passe = null;

    #[ORM\Column]
    private array $roles = [];

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
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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


    public function getRole(): string
    {
        $roles = $this->getRoles();
        return in_array('ROLE_ADMIN', $roles) ? 'ROLE_ADMIN' : null;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_ADMIN';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
}
