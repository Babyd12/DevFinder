<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BriefRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Hostname;

#[ORM\Entity(repositoryClass: BriefRepository::class)]

#[ApiResource(
    shortName:'Module gestion de publication brief -Administrateur',
)]

#[GetCollection(
    uriTemplate: 'brief/liste',
    forceEager: false,
    normalizationContext: [ 'groups' => ['brief:index'] ],
    denormalizationContext:[ 'groups' => ['brief:index'] ],
    outputFormats: [ 'json' => 'application/json']
)]

#[Get(
    uriTemplate: 'brief/{id}',
    forceEager: true,
    normalizationContext: [ 'groups' => ['brief:show'] ],
    denormalizationContext: [ 'groups' => ['brief:show']],
)]

#[Post(
    uriTemplate: 'brief/publier',
    security: "is_granted('ROLE_ADMIN') ",
    normalizationContext: [ 'groups' => ['brief:create']],
    denormalizationContext: [ 'groups' => ['brief:create'] ],
)]

#[Put(
    uriTemplate: 'brief/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
    normalizationContext: [ 'groups' => ['brief:update']],
    denormalizationContext: [ 'groups' => ['brief:update'] ]

)]

#[Delete(
    uriTemplate: 'brief/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
)]

class Brief
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['brief:show', 'brief:index', 'brief:create', 'brief:update'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['brief:show', 'brief:index', 'brief:create', 'brief:update'])]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 50, minMessage: 'veuillez saisir au moins 50 lettres')]
    #[Assert\Type(type:'string', message: 'La valeur {{ value }} doit être de type {{ type }}.')]
    #[Assert\Regex('/^[a-zA-Z0-9À-ÿ\s]*$/', message: 'Le format du texte saisi est incorrecte.  ')]
    #[Groups(['brief:show', 'brief:index', 'brief:create', 'brief:update'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['brief:show', 'brief:create', 'brief:update'])]
    private ?string $lient_support = null;

    #[ORM\Column(length: 255)]
    #[Groups(['brief:show', 'brief:index', 'brief:create', 'brief:update'])]
    #[Assert\Regex('/^[a-zA-Z0-9À-ÿ\s]*$/', message: 'Le format du texte saisi est incorrecte.')]
    private ?string $niveau_de_competence = null;

    #[ORM\OneToMany(mappedBy: 'brief', targetEntity: Livrable::class)]
    private Collection $livrables;

    

 

    public function __construct()
    {
        $this->livrables = new ArrayCollection();
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

    public function getLientSupport(): ?string
    {
        return $this->lient_support;
    }

    public function setLientSupport(string $lient_support): static
    {
        $this->lient_support = $lient_support;

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

    /**
     * @return Collection<int, Livrable>
     */
    public function getLivrables(): Collection
    {
        return $this->livrables;
    }

    public function addLivrable(Livrable $livrable): static
    {
        if (!$this->livrables->contains($livrable)) {
            $this->livrables->add($livrable);
            $livrable->setBrief($this);
        }

        return $this;
    }

    public function removeLivrable(Livrable $livrable): static
    {
        if ($this->livrables->removeElement($livrable)) {
            // set the owning side to null (unless already changed)
            if ($livrable->getBrief() === $this) {
                $livrable->setBrief(null);
            }
        }

        return $this;
    }

 

    
}
