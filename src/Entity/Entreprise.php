<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\EntrepriseRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
// #[ApiResource(
//     shortName: 'Module gestion de recrutement -Entreprise',
//     operations: [
//         new Get(
//             uriTemplate: 'entreprise/recruter/apprenant/{id}',
            
//         ),
//     ]
// )]


// #[GetCollection(
//     shortName: 'Module gestion de compte -Entreprise',
//     uriTemplate: 'entreprise/liste',
//     description: 'Modifie toi',
//     name: 'nom temporaire',
//     normalizationContext: [ 'groups' => ['association:index'] ]
// )]

// #[Get(
//     shortName: 'Module gestion de compte -Entreprise',
//     uriTemplate: 'entreprise/show',

//     forceEager: true,
//     normalizationContext: [ 'groups' => ['association:show'] ]
// )]

// #[Post(
//     shortName: 'Module gestion de compte -Entreprise',
//     uriTemplate: 'entreprise/inscription',
//     denormalizationContext: [ 'groups' => ['association:create'] ]
// )]

// #[Put(
//     shortName: 'Module gestion de compte -Entreprise',
//     uriTemplate: 'entreprise/update',
//     denormalizationContext: [ 'groups' => ['association:update'] ]
// )]

// #[Patch(
//     shortName: 'Module gestion de compte -Entreprise',
//     uriTemplate: 'entreprise/change_mot_de_passe',
//     denormalizationContext: [ 'groups' => ['association:updateOne'] ]
// )]

// #[Delete(
//     shortName: 'Module gestion de compte -Entreprise',
//     uriTemplate: 'entreprise/supprimerCompte',
// )]

class Entreprise implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['association:show', 'association:index', 'association:create', 'association:update'])]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255)]
    #[Groups(['association:show', 'association:index', 'association:create', 'association:update'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Groups(['association:show', 'association:index', 'association:create', 'association:update'])]
    private ?string $mot_de_passe = null;

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
}
