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
use App\Controller\CustumAdminController;
use App\Controller\GetUserLoggedController;
use App\Controller\MeController;
use App\Repository\AdministrateurRepository;
use App\State\AdminStateProvider;
use App\State\GetUserLoggedInfoStateProvier;
use App\State\GetUserLoggedProcessor;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]

#[ApiResource(
   
    operations: [
        new Post(
            shortName: 'Deconnexion',
            uriTemplate:'/deconnexion',
            routeName: 'app_logout',
            security: "is_granted('ROLE_APPRENANT') or is_granted('ROLE_ASSOCIATION') or is_granted('ROLE_ADMINISTRATEUR') or is_granted('ROLE_ENTREPRISE') ",
            normalizationContext: ['groups' => 'apprenantPojet:show'],
            denormalizationContext: ['groups' => 'apprenant:participate'],
            securityMessage: 'Only authenticated users can access this resource.',
        ),

        new Post(
            shortName: 'Récuperer l\'utilisateur connecté',
            processor:GetUserLoggedProcessor::class,
            uriTemplate:'/utilisateur/connecte',
            denormalizationContext: ['groups' => 'apprenant:connecte'],
        ),
        new GetCollection(
            shortName: 'Module gestion de compte -Administrateur',
            uriTemplate:'/administrateur/liste/utilisateur',
            provider:CustumAdminController::class,
            name: 'app_custum_admin_utilisateur',
            security:  "is_granted('ROLE_ADMIN')",
            schemes: [
                'liste des developpeur' => Apprenant::class,
            ]
        ),

        // new GetCollection(
        //     shortName: 'Module gestion de compte -Administrateur',
        //     uriTemplate:'/administrateur/liste/entreprises',
        //     provider:CustumAdminController::class,
        //     name: 'app_custum_admin_entreprises',
        //     security:  "is_granted('ROLE_ADMIN')",
        // ),
        // new GetCollection(
        //     shortName: 'Module gestion de compte -Administrateur',
        //     uriTemplate:'/administrateur/liste/associations',
        //     name: 'app_custum_admin_associations',
        //     provider:CustumAdminController::class,
        // ),
        // new GetCollection(
        //     shortName: 'Module gestion de compte -Administrateur',
        //     uriTemplate:'/administrateur/liste/developpeurs',
        //     provider:CustumAdminController::class,
        //     name: 'app_custum_admin_developpeurs',

        // ),
    ]
)]

#[GetCollection(
    shortName: 'Module gestion de publication -Administrateur',
    uriTemplate: '/administrateur',
    normalizationContext: ['groups' => ['admin:index']],
    // provider: GetUserLoggedInfoStateProvier::class,
)]

#[Put(
    shortName: 'Module gestion de publication -Administrateur',
    requirements: ['id' => '\d+'],
    uriTemplate: '/administrateur/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') and previous_object.getUserIdentifier() == user.getUserIdentifier() ",
    denormalizationContext: ['groups' => ['admin:update']],
    normalizationContext: ['groups' => ['admin:updateRead']],
)]

#[Patch(
    shortName: 'Module gestion de publication -Administrateur',
    requirements: ['id' => '\d+'],
    uriTemplate: '/administrateur/change_password{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') and previous_object.getUserIdentifier() == user.getUserIdentifier() ",
    formats: ['json' => 'application/json'],
    denormalizationContext: ['groups' => ['admin:updateOne']],
    // normalizationContext: ['groups' => ['admin:updateOne']],
)]


class Administrateur  implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['admin:index', 'admin:update', 'admin:updateOne'])]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255)]
    #[Groups(['admin:index', 'admin:update', 'admin:updateOne'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['admin:updateOne'])]
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
    /**
     * @see UserInterface
     *     
     */
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

    public static function createFromPayload($username,  array $playload)
    {
        $user = new Administrateur();
        return $user;
    }

    public function __toString()
    {
        return $this->getNomComplet(); // Adapté à votre entité, utilisez la méthode ou les propriétés nécessaires.
    }


    public function getUserLogged()
    {
        return new JsonResponse([
            'Nom complet' => $this->getNomComplet(),
            'email' => $this->getUserIdentifier(),
            'role' => $this->getRole()
    ]);
    }

}
