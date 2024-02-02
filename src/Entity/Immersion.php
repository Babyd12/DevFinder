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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Hostname;

#[ORM\Entity(repositoryClass: ImmersionRepository::class)]

#[ApiResource(
    shortName: 'Module gestion de publication immersion -Administrateur',
)]

#[GetCollection(
    uriTemplate: 'immersion/liste',
    forceEager: false,
    normalizationContext: ['groups' => ['immersion:index']],
    denormalizationContext: ['groups' => ['immersion:index']],
)]

#[Get(
    uriTemplate: 'immersion/{id}',
    forceEager: true,
    normalizationContext: ['groups' => ['immersion:show']],
    denormalizationContext: ['groups' => ['immersion:show']],
)]

#[Post(
    uriTemplate: 'immersion/publier',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
    normalizationContext: ['groups' => ['immersion:create']],
    denormalizationContext: ['groups' => ['immersion:create']],
)]

#[Put(
    uriTemplate: 'immersion/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
    normalizationContext: ['groups' => ['immersion:update']],
    denormalizationContext: ['groups' => ['immersion:update']]

)]

#[Delete(
    uriTemplate: 'immersion/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') ",
)]

class Immersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:create', 'immersion:update'])]
    private ?string $titre = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 35, max: 250, minMessage: 'Veuillez saisir au minimum 35 caractères', maxMessage: 'Veuillez saisir moins 250 caractères',)]
    #[Assert\Regex('/^[a-zA-Z0-9À-ÿ\s]*$/', message: 'Le format du texte saisi est incorrecte.  ')]
    #[Groups(['immersion:show', 'immersion:index', 'immersion:create', 'immersion:update'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(
        message: 'L\'url {{ value }} n\'est pas une url valide',
    )]
    #[Groups(['immersion:show', 'immersion:create', 'immersion:update'])]
    private ?string $lien_support = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        '/^https:\/\/github\.com\/[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+\/?$/',
        message: "Le lien GitHub '{{ value }}' n'est pas valide."
    )]
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
