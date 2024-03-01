<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\LivrableRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: LivrableRepository::class)]
#[ApiResource(
    shortName: 'Module gestion de publication livrable -Apprenant',

)]

#[Post(
    uriTemplate: 'apprenant/soumettre/livrable',
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
/**
 * @Reste à denormaliser toute les relations de livrables pour
 */
#[UniqueEntity(
    fields: ['lien_du_livrable', 'apprenant'],
    errorPath: 'Lien du livrable',
    message: "Vous tentez d'ajouter un lien déjà existant. Veuillez le modifier si c'est le votre",
)]

#[UniqueEntity(
    fields: ['brief', 'apprenant'],
    errorPath: 'Doublon',
    message: "Vous pouvez pas ajouter deux fois le même livrable pour une brief",
)]

#[UniqueEntity(
    fields: ['immersion', 'apprenant'],
    errorPath: 'Doublon',
    message: "Vous pouvez pas ajouter deux fois le même livrable pour une immersion",
)]

// #[ApiFilter(SearchFilter::class, properties: ['tite' => 'brief.titre', 'price' => 'exact', 'description' => 'partial'])]
#[ApiFilter(SearchFilter::class, properties: ['brief.titre' => 'ipartial', 'immersion.titre' => 'ipartial'])]
class Livrable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(
        [
            'livrable:index', 'livrable:show',
             /**
             * @info Quand j'affiche une immersion qui a enregistré un livrable affiche le lien du livrable au lieu de l'uri
             */
            'immersion:show', 'immersion:index',
            'brief:show', 'brief:index', 
        ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        '/^https:\/\/github\.com\/[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+\/?$/',
        message: "Le lien GitHub ou similaire '{{ value }}' n'est pas valide."
    )]
    #[Groups(
        [
            'livrable:create', 'livrable:index', 'livrable:show', 'livrable:update', 'livrable:updateOne',
            /**
             * @info Quand j'affiche une immersion qui a enregistré un livrable affiche le lien du livrable au lieu de l'uri
             */
            'immersion:show', 'immersion:index',
            'brief:show', 'brief:index',

        ]
    )]
    private ?string $lien_du_livrable = null;

    #[ORM\ManyToOne(inversedBy: 'livrables')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['livrable:create', 'livrable:index', 'livrable:show', 'livrable:update', 'livrable:updateOne'])]
    private ?Apprenant $apprenant = null;

    #[ORM\ManyToOne(inversedBy: 'livrables')]
    #[Groups(['livrable:create', 'livrable:index', 'livrable:show', 'livrable:update', 'livrable:updateOne'])]
    private ?Brief $brief = null;

    #[ORM\ManyToOne(inversedBy: 'livrables')]
    #[Groups(['livrable:create', 'livrable:index', 'livrable:show', 'livrable:update', 'livrable:updateOne'])]
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
