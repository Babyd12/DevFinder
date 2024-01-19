<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ApprenantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApprenantRepository::class)]
#[ApiResource]
class Apprenant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mot_de_passe = null;

    #[ORM\ManyToOne(inversedBy: 'apprenants')]
    private ?Immersion $immersion = null;

    #[ORM\ManyToMany(targetEntity: Brief::class, mappedBy: 'apprenant')]
    private Collection $briefs;

    public function __construct()
    {
        $this->briefs = new ArrayCollection();
    }

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

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;

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

    /**
     * @return Collection<int, Brief>
     */
    public function getBriefs(): Collection
    {
        return $this->briefs;
    }

    public function addBrief(Brief $brief): static
    {
        if (!$this->briefs->contains($brief)) {
            $this->briefs->add($brief);
            $brief->addApprenant($this);
        }

        return $this;
    }

    public function removeBrief(Brief $brief): static
    {
        if ($this->briefs->removeElement($brief)) {
            $brief->removeApprenant($this);
        }

        return $this;
    }
}
