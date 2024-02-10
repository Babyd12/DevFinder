<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CompetenceRepository;
use App\State\AddUserToRelationProcessor;
use App\State\RemoveUserToRelationProcessor;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompetenceRepository::class)]

#[ApiResource(
    shortName: 'Module gestion de compte -Apprenant'
)]

#[GetCollection(
    uriTemplate: '/competence/liste',
    description: 'Affiche toutes les compétences',
    normalizationContext: ['groups' => ['competence:index']],

)]

#[Get(
    forceEager: true,
    uriTemplate: '/competence/{id}',
    normalizationContext: ['groups' => ['competence:show']],


)]

#[Post(
    uriTemplate: '/competence/ajouter',
    processor: AddUserToRelationProcessor::class,
    security: "is_granted('ROLE_APPRENANT')",
    denormalizationContext: ['groups' => ['competence:create']]
)]

#[Put(
    uriTemplate: '/competence/{id}',
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getApprenant(user) == user ",
    securityMessage: 'Sorry, but you are not this competence owner.',
    denormalizationContext: ['groups' => ['competence:update']]
)]

#[Delete(
    uriTemplate: '/competence/{id}',
    processor: RemoveUserToRelationProcessor::class,
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getApprenant(user) == user ",
)]

#[UniqueEntity(
    fields: ['nom', 'apprenant'],
    errorPath: 'apprenant',
    message: 'Cette compétence existe déjà pour cet apprenant.',
)]

class Competence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(
        [
            'competence:show',
            'apprenant:show'
        ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(
        [
            'competence:show', 'competence:index', 'competence:create', 'competence:update',
            'apprenant:show'
        ]
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide')]
    #[Assert\Length(min: 35, max: 250, minMessage: 'Veuillez saisir au minimum 35 caractères', maxMessage: 'Veuillez saisir moins 250 caractères',)]
    #[Groups(
        [
            'competence:show', 'competence:index', 'competence:create', 'competence:update',
            'apprenant:show'
        ]
    )]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Apprenant::class, inversedBy: 'competences')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['competence:show'])]
    private ?Apprenant $apprenant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getApprenant(): ?Apprenant
    {
        return $this->apprenant;
    }

    public function setApprenant(?Apprenant $apprenant): static
    {
        $this->apprenant = $apprenant;

        return $this;
    }
}
