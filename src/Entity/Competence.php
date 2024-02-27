<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    shortName: 'Module gestion de competence -Administrateur',
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
    // processor: AddUserToRelationProcessor::class,
    security: "is_granted('ROLE_ADMIN')",
    securityMessage: 'Vous navez pas les droit requis pour effectuer cette action.',
    denormalizationContext: ['groups' => ['competence:create']]
)]

#[Put(
    uriTemplate: '/competence/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
    securityMessage: 'Vous navez pas les droit requis pour effectuer cette action.',
    denormalizationContext: ['groups' => ['competence:update']]
)]

#[Delete(
    uriTemplate: '/competence/{id}',
    processor: RemoveUserToRelationProcessor::class,
    securityPostDenormalize: "is_granted('ROLE_ADMIN')",
    securityMessage: 'Vous navez pas les droit requis pour effectuer cette action.',
)]

#[UniqueEntity(
    fields: ['nom',],
    errorPath: 'Nom competence',
    message: 'Cette compétence existe déjà.',
)]

class Competence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(
        [
            'competence:show', 'competence:index',
             /**
             * ici lorsque jaffiche un apprenant ayant enregistré une compétence, 
             * je charge les informations de la dite compétence au lieu de l'uri
             * @see src/Entity/Apprenant
             * 
             */
            'apprenant:show',
            'descriptionCompetence:show', 
        ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(
        [
            'competence:show', 'competence:index', 'competence:create', 'competence:update',
             /**
             * ici lorsque jaffiche un descriptionCompétence contenant une compétence, 
             * je charge les informations de la dite compétence au lieu de l'uri
             * @see src/Entity/Apprenant
             * 
             */
            'descriptionCompetence:show', 'descriptionCompetence:index',
        ]
    )]
    private ?string $nom = null;
    


    #[ORM\OneToMany(mappedBy: 'competence', targetEntity: DescriptionCompetence::class)]
    private Collection $descriptionCompetences;

    public function __construct()
    {
        $this->descriptionCompetences = new ArrayCollection();
    }

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

 

    /**
     * @return Collection<int, DescriptionCompetence>
     */
    public function getDescriptionCompetences(): Collection
    {
        return $this->descriptionCompetences;
    }

    public function addDescriptionCompetence(DescriptionCompetence $descriptionCompetence): static
    {
        if (!$this->descriptionCompetences->contains($descriptionCompetence)) {
            $this->descriptionCompetences->add($descriptionCompetence);
            $descriptionCompetence->setCompetence($this);
        }

        return $this;
    }

    public function removeDescriptionCompetence(DescriptionCompetence $descriptionCompetence): static
    {
        if ($this->descriptionCompetences->removeElement($descriptionCompetence)) {
            // set the owning side to null (unless already changed)
            if ($descriptionCompetence->getCompetence() === $this) {
                $descriptionCompetence->setCompetence(null);
            }
        }

        return $this;
    }


}
