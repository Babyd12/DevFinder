<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Entity\Apprenant;
use App\Entity\Competence;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\AddUserToRelationProcessor;
use App\State\GetUserAndHerRelationsProvider;
use App\Repository\DescriptionCompetenceRepository;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: DescriptionCompetenceRepository::class)]
#[ApiResource(
    shortName: 'Module gestion de competence -Apprenant',
    routePrefix: 'apprenant',
    operations: [
        new Get(
            uriTemplate:'/mesCompetences/{id}',
        
        ),
    ]

)]

#[GetCollection(
    uriTemplate: '/descriptionCompetence/liste',
    normalizationContext: ['groups' => ['descriptionCompetence:index']],

)]

#[Get(
    uriTemplate: '/descriptionCompetence/{id}',
    normalizationContext: ['groups' => ['descriptionCompetence:show']],
    
)]

#[Post(
    uriTemplate: '/descriptionCompetence/ajouter',
    security: "is_granted('ROLE_APPRENANT')",
    processor: AddUserToRelationProcessor::class,
    denormalizationContext: ['groups' => ['descriptionCompetence:create']],
)]

#[Put(
    uriTemplate: '/descriptionCompetence/{id}',
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getApprenant().getUserIdentifier() == user.getUserIdentifier()",
    denormalizationContext: ['groups' => ['descriptionCompetence:update']],
)]

#[Delete(
    uriTemplate: '/descriptionCompetence/{id}',
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') and previous_object.getApprenant().getUserIdentifier() == user.getUserIdentifier()",
    denormalizationContext: ['groups' => ['descriptionCompetence:delete']],
)]

#[ApiFilter(SearchFilter::class, properties:['apprenant.id'=> 'ipartial', 'competence.nom'=>'ipartial'])]
class DescriptionCompetence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(
        [
            'descriptionCompetence:index',
            'descriptionCompetence:show',
            'descriptionCompetence:update',
            'descriptionCompetence:delete',
            /**
             * ici lorsque jaffiche un apprenant ayant enregistré une compétence, 
             * je charge les informations de la dite compétence au lieu de l'uri
             * @see src/Entity/Apprenant
             * 
             */
            'apprenant:show', 
          
        ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(
        [
            'descriptionCompetence:index',
            'descriptionCompetence:show',
            'descriptionCompetence:create',
            'descriptionCompetence:update',
            'descriptionCompetence:delete',
            /**
             * ici lorsque jaffiche un apprenant ayant enregistré une compétence, 
             * je charge les informations de la dite compétence au lieu de l'uri
             * @see src/Entity/Apprenant
             * 
             */
            'apprenant:show'
        ]
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(
        [
            'descriptionCompetence:index',
            'descriptionCompetence:show',
            'descriptionCompetence:create',
            'descriptionCompetence:update',
            'descriptionCompetence:delete',
             /**
             * ici lorsque jaffiche un apprenant ayant enregistré une compétence, 
             * je charge les informations de la dite compétence au lieu de l'uri
             * @see src/Entity/Apprenant
             * 
             */
            'apprenant:show'
        ]
    )]
    private ?string $lien_de_realisation = null;



    #[ORM\ManyToOne(inversedBy: 'descriptionCompetences')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(
        [
            'descriptionCompetence:index',
            'descriptionCompetence:show',
            'descriptionCompetence:delete',
        ]
    )]
    
    // #[ApiFilter(SearchFilter::class, properties:['apprenant.id'=> 'ipartial'])]
    private ?Apprenant $apprenant = null;
    #[ORM\ManyToOne(inversedBy: 'descriptionCompetences')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(
        [
            'descriptionCompetence:index',
            'descriptionCompetence:show',
            'descriptionCompetence:create',
            'descriptionCompetence:update',
            'descriptionCompetence:delete',

          
        ]
    )]
    private ?Competence $competence = null;


    /**
     * @info Start Getter and setter 
     */
    public function getId(): ?int
    {
        return $this->id;
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

    public function getLienDeRealisation(): ?string
    {
        return $this->lien_de_realisation;
    }

    public function setLienDeRealisation(string $lien_de_realisation): static
    {
        $this->lien_de_realisation = $lien_de_realisation;

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

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): static
    {
        $this->competence = $competence;

        return $this;
    }

    // public function getAllDescriptionCompetencesForOneApprenant($id)
    // {
    //     $apprenant = 
    // }
}
