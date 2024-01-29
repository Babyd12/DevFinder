<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\State\SetUserToRelationClass;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CompetenceRepository;
use App\State\RemoveUserToRelationClass;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompetenceRepository::class)]
/*
#[ApiResource(
    shortName: 'Module gestion de compte -Apprenant'
)]

#[GetCollection(
    uriTemplate:'/competence/liste',
    description: 'Affiche toutes les compétences',
    normalizationContext: [ 'groups' => ['competence:index'] ]
)]

#[Get(
    forceEager: true,
    uriTemplate:'/competence/{id}',
    normalizationContext: [ 'groups' => ['competence:show'] ]
)]

#[Post(
    uriTemplate:'/competence/ajouter',
    processor:SetUserToRelationClass::class,
    security: "is_granted('ROLE_APPRENANT')",
    denormalizationContext: [ 'groups' => ['competence:create'] ]
)]

#[Put(
    uriTemplate:'/competence/{id}',
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getApprenant(user) == user ",
    securityMessage: 'Sorry, but you are not this competence owner.',
    denormalizationContext: [ 'groups' => ['competence:update'] ]
)]

#[Delete(
    uriTemplate:'/competence/{id}',
    processor:RemoveUserToRelationClass::class,
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getApprenant(user) == user ",
)]
*/

class Competence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['competence:show', 'competence:index', 'competence:create', 'competence:update'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['competence:show', 'competence:index', 'competence:create', 'competence:update'])]
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
