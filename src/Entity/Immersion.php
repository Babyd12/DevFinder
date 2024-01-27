<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ImmersionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Hostname;

#[ORM\Entity(repositoryClass: ImmersionRepository::class)]
/*
#[ApiResource(
    shortName: 'Module gestion de publication immersion -Administrateur',
)]

#[GetCollection(
    forceEager: false,
    uriTemplate:'/immersion/liste',
    normalizationContext: [ 'groups' => ['immersion:index'] ]
)]

#[Get(
    forceEager: true,
    uriTemplate:'/immersion/show',
    normalizationContext: [ 'groups' => ['immersion:show'] ]
)]

#[Post(
    uriTemplate:'/immersion/ajouter',
    denormalizationContext: [ 'groups' => ['immersion:create'] ]
)]

#[Put(
    uriTemplate:'/immersion/update',
    denormalizationContext: [ 'groups' => ['immersion:update'] ]
)]

#[Delete(
    uriTemplate:'/immersion/supprimer',
)]
*/
class Immersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:create', 'immersion:update'])]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:create', 'immersion:update'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['immersion:show', 'immersion:create', 'immersion:update'])]
    private ?string $lien_support = null;

    #[ORM\Column(length: 255)]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:create', 'immersion:update'])]
    private ?string $niveau_de_competence = null;

    #[ORM\Column(length: 255)]
    #[Groups(['immersion:show', 'immersion:create', 'immersion:update'])]
    private ?string $lien_du_livrable = null;

    #[ORM\OneToMany(mappedBy: 'immersion', targetEntity: Apprenant::class)]
    #[Groups(['immersion:show'])]
    private Collection $apprenants;

    public function __construct()
    {
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

    public function getLienSupport(): ?string
    {
        return $this->lien_support;
    }

    public function setLienSupport(string $lien_support): static
    {
        $this->lien_support = $lien_support;

        return $this;
    }

    public function getNiveauDeCompetence(): ?string
    {
        return $this->niveau_de_competence;
    }

    public function setNiveauDeCompetence(string $niveau_de_competence): static
    {
        $this->niveau_de_competence = $niveau_de_competence;

        return $this;
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
            $apprenant->setImmersion($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): static
    {
        if ($this->apprenants->removeElement($apprenant)) {
            // set the owning side to null (unless already changed)
            if ($apprenant->getImmersion() === $this) {
                $apprenant->setImmersion(null);
            }
        }

        return $this;
    }
}
