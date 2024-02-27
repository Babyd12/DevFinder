<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\LivrableRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivrableRepository::class)]
#[ApiResource(
    shortName: 'Module gestion de publication livrable -Apprenant',

)]

#[Post(
    uriTemplate: 'apprenant/livrer/immersion',
    security: "is_granted('ROLE_APPRENANT') ",
    denormalizationContext: ['groups' => 'livrable:create'],
)]


#[Get(
    uriTemplate: 'livrable/{id}',
    normalizationContext: ['groups' => 'livrable:show'],
)]
#[GetCollection(
    uriTemplate: 'livrables',
    normalizationContext: ['groups' => 'livrable:index'],
)]

#[Put(
    uriTemplate: 'livrable/brief/{id}',
    denormalizationContext: ['groups' => 'livrableBrief:updateOne'],
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getApprenant().getUserIdentifier() == user.getUserIdentifier() ",

)]

#[Delete(
    uriTemplate: 'livrable/{id}',
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getApprenant().getUserIdentifier() == user.getUserIdentifier() ",

)]

class Livrable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['livrable:index', 'livrable:show'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        '/^https:\/\/github\.com\/[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+\/?$/',
        message: "Le lien GitHub ou similaire '{{ value }}' n'est pas valide."
    )]
    #[Groups(['livrable:create','livrable:index', 'livrable:show', 'livrable:update', 'livrable:updateOne'])]
    private ?string $lien_du_livrable = null;

    #[ORM\ManyToOne(inversedBy: 'livrables')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['livrable:create','livrable:index', 'livrable:show', 'livrable:update', 'livrable:updateOne'])]
    private ?Apprenant $apprenant = null;

    #[ORM\ManyToOne(inversedBy: 'livrables')]
    #[Groups(['livrable:create','livrable:index', 'livrable:show', 'livrable:update', 'livrable:updateOne'])]
    private ?Brief $brief = null;

    #[ORM\ManyToOne(inversedBy: 'livrables')]
    #[Groups(['livrable:create','livrable:index', 'livrable:show', 'livrable:update', 'livrable:updateOne'])]
    private ?Immersion $immersion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLienDuLivrable(): ?string
    {
        return $this->lien_du_livrable;
    }

    public function setLienDuLivrable(string $lien_du_livrable): static
    {
        $this->lien_du_livrable = $lien_du_livrable;

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
    
    public function getBrief(): ?Brief
    {
        return $this->brief;
    }

    public function setBrief(?Brief $brief): static
    {
        $this->brief = $brief;

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
}
