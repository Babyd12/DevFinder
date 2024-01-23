<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjetRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\CustomApprenantController;
use App\Controller\CustomProjetController;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\DocBlock\Tag;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Hostname;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_USER')",
    operations: [
        new Get(
            shortName: 'Module Gestion Participation Projet',
            uriTemplate: '/apprenant/participer/projet-{projectId}',
            // routeName: 'participateToProject',
            controller: CustomProjetController::class,
            normalizationContext: [ 'groups' => 'apprenantPojet:show' ],
            denormalizationContext: ['groups' => 'apprenant:participate'],
            
        ),
        new Get(
            shortName: 'Module Gestion Participation Projet',
            name: 'publication',
            uriTemplate: '/apprenant/quitter/projet-{projectId}-{apprenantId}',
            // routeName: 'participateToProject',
            controller: CustomProjetController::class,
            normalizationContext: [ 'groups' => 'apprenantPojet:show' ],
            denormalizationContext: ['groups' => 'apprenant:participate'],
            
        ),

        
       
    ]
)]

#[GetCollection(
  
    description: 'Affiche tout les projet',
    name:'un nom simple a comprndre',
    normalizationContext: [ 'groups' => ['projet:index'] ]
)]

#[Get(
    forceEager: true,
    normalizationContext: [ 'groups' => ['projet:show'] ]
)]

#[Post(
    denormalizationContext: [ 'groups' => ['projet:create'] ]
)]

#[Put(
    denormalizationContext: [ 'groups' => ['projet:update'] ]
)]

#[Delete()]


class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['projet:show', 'projet:index', 'projet:create', 'projet:update'])]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Groups(['projet:show', 'projet:index', 'projet:create', 'projet:update'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['projet:show', 'projet:index', 'projet:create', 'projet:update'])]
    private ?int $nombre_de_participant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['projet:show', 'projet:index', 'projet:create', 'projet:update'])]
    private ?\DateTimeInterface $date_limite = null;

    #[ORM\ManyToOne(inversedBy: 'projets')]
    #[Groups([ 'projet:create', 'projet:show'])]
    private ?Association $association = null;

    #[ORM\ManyToMany(targetEntity: LangageDeProgrammation::class, inversedBy: 'projets')]
    #[Groups(['projet:show'])]
    private Collection $langage_de_programmation;

    #[ORM\ManyToMany(targetEntity: Apprenant::class, mappedBy: 'projet')]
    private Collection $apprenants;

    public function __construct()
    {
        $this->langage_de_programmation = new ArrayCollection();
        $this->apprenants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getNombreDeParticipant(): ?int
    {
        return $this->nombre_de_participant;
    }

    public function setNombreDeParticipant(int $nombre_de_participant): static
    {
        $this->nombre_de_participant = $nombre_de_participant;

        return $this;
    }

    public function getDateLimite(): ?\DateTimeInterface
    {
        return $this->date_limite;
    }

    public function setDateLimite(\DateTimeInterface $date_limite): static
    {
        $this->date_limite = $date_limite;

        return $this;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): static
    {
        $this->association = $association;

        return $this;
    }

    /**
     * @return Collection<int, LangageDeProgrammation>
     */
    public function getLangageDeProgrammation(): Collection
    {
        return $this->langage_de_programmation;
    }

    public function addLangageDeProgrammation(LangageDeProgrammation $langageDeProgrammation): static
    {
        if (!$this->langage_de_programmation->contains($langageDeProgrammation)) {
            $this->langage_de_programmation->add($langageDeProgrammation);
        }

        return $this;
    }

    public function removeLangageDeProgrammation(LangageDeProgrammation $langageDeProgrammation): static
    {
        $this->langage_de_programmation->removeElement($langageDeProgrammation);

        return $this;
    }

    /**
     * @return Collection<int, Apprenant>
     */
    public function getApprenants(): Collection
    {
        return $this->apprenants;
    }

    public function addApprenant(Apprenant $apprenant): static
    {
        if (!$this->apprenants->contains($apprenant)) {
            $this->apprenants->add($apprenant);
            $apprenant->addProjet($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): static
    {
        if ($this->apprenants->removeElement($apprenant)) {
            $apprenant->removeProjet($this);
        }

        return $this;
    }
}
