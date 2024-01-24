<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\LangageDeProgrammationRepository;

#[ORM\Entity(repositoryClass: LangageDeProgrammationRepository::class)]
#[ApiResource(
    shortName:'Module gestion de publication Langage de programmation -Association',
)]

#[GetCollection(
    uriTemplate:'/langage/liste',
    normalizationContext: [ 'groups' => ['langageDeProgrammation:index'] ]
)]

#[Get(
    uriTemplate:'/langage/show',
    forceEager: true,
    normalizationContext: [ 'groups' => ['langageDeProgrammation:show'] ]
)]

#[Post(
    uriTemplate:'/langage/ajouter',
    denormalizationContext: [ 'groups' => ['langageDeProgrammation:create'] ]
)]

#[Put(
    uriTemplate:'/langage/update',
    denormalizationContext: [ 'groups' => ['langageDeProgrammation:update'] ]
)]

#[Delete(
    uriTemplate:'/langage/supprimer',
)]


class LangageDeProgrammation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['langageDeProgrammation:show', 'langageDeProgrammation:index', 'langageDeProgrammation:create', 'langageDeProgrammation:update'])]
    private ?string $nom = null;

    #[ORM\ManyToMany(targetEntity: Projet::class, mappedBy: 'langage_de_programmation')]
    #[Groups(['langageDeProgrammation:show'])]
    private Collection $projets;

    #[ORM\ManyToOne(inversedBy: 'langageDeProgrammations')]
    private ?Association $association = null;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
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
     * @return Collection<int, Projet>
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): static
    {
        if (!$this->projets->contains($projet)) {
            $this->projets->add($projet);
            $projet->addLangageDeProgrammation($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): static
    {
        if ($this->projets->removeElement($projet)) {
            $projet->removeLangageDeProgrammation($this);
        }

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
}
