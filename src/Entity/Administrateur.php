<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use MessageFormatter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Controller\MeController;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\State\GetUserLoggedProcessor;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\CustumAdminController;
use App\Controller\GetUserLoggedController;
use App\Repository\AdministrateurRepository;
use App\State\GetUserLoggedInfoStateProvier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]

#[ApiResource(

    operations: [
        // new Post(
        //     shortName: 'Deconnexion',
        //     uriTemplate: '/deconnexion',
        //     routeName: 'app_logout',
        //     security: "is_granted('ROLE_APPRENANT') or is_granted('ROLE_ASSOCIATION') or is_granted('ROLE_ADMINISTRATEUR') or is_granted('ROLE_ENTREPRISE') ",
        //     normalizationContext: ['groups' => 'apprenantPojet:show'],
        //     denormalizationContext: ['groups' => 'apprenant:participate'],
        //     securityMessage: 'Vous devez être connecté.',
        // ),

        new Post(
            shortName: 'Mot de passe oublié',
            // uriTemplate: '/api/test/motDePasseOublie',
            denormalizationContext: ['groups' => 'admin:restorer'],
            inputFormats:['multipart'=>'multipart/form-data'],  
            controller:CustumAdminController::class,
            name: 'app_mot_de_passe_oublie',
        ),

        new Post(
            shortName: 'Récuperer l\'utilisateur connecté V2',
            controller: CustumAdminController::class,
            name: 'app_admin_recuperer_utilisateur_connecter',
            uriTemplate: '/utilisateur/connecte',
            denormalizationContext: ['groups' => 'apprenant:connecte'],
            // outputFormats: ['json' => 'application/json'],
        ),
        new GetCollection(
            shortName: 'Module gestion de compte -Administrateur',
            uriTemplate: '/administrateur/liste/utilisateurs',
            provider: CustumAdminController::class,
            name: 'app_custum_admin_listeUtilisateur',
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => 'administrateur:connecte'],

        ),
        new Get(
            shortName: 'Module gestion de compte -Administrateur',
            uriTemplate: '/administrateur/liste/utilisateursBloque',
            provider: CustumAdminController::class,
            name: 'app_custum_admin_listeUtilisateurBloquer',
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => 'administrateur:utilisateurBloquer'],
        ),
    ]
)]

#[GetCollection(
    shortName: 'Module gestion de compte -Administrateur',
    uriTemplate: '/administrateur',
    normalizationContext: ['groups' => ['admin:index']],
    // provider: GetUserLoggedInfoStateProvier::class,
)]

#[Put(
    shortName: 'Module gestion de compte -Administrateur',
    requirements: ['id' => '\d+'],
    uriTemplate: '/administrateur/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
    denormalizationContext: ['groups' => ['admin:update']],
    normalizationContext: ['groups' => ['admin:updateRead']],
)]

#[Patch(
    shortName: 'Module gestion de compte -Administrateur',
    requirements: ['id' => '\d+'],
    uriTemplate: '/administrateur/change_password{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN')",
    formats: ['json' => 'application/json'],
    denormalizationContext: ['groups' => ['admin:updateOne']],
    // normalizationContext: ['groups' => ['admin:updateOne']],
)]


// #[ApiFilter(SearchFilter::class, properties: ['brief.titre' => 'ipartial', 'immersion.titre' => 'ipartial'])]
class Administrateur  implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['admin:index', 'admin:update', ])]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9]{1,}([.]{0,1}[a-zA-Z0-9]+){1,26}@[a-z]+[.][a-z]{2,}$/',
        message: 'Veuillez entrer un format d\'email correcte.'
    )]
    #[Groups(['admin:index', 'admin:update','admin:restorer'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['admin:updateOne'])]
    private ?string $mot_de_passe = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?bool $etat = null;


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

    public function getUserLogged()
    {
        return new JsonResponse([
            'Nom complet' => $this->getNomComplet(),
            'email' => $this->getUserIdentifier(),
            'role' => $this->getRole()
        ]);
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

