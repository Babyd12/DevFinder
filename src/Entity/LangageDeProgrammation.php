<?php

namespace App\Entity;

use App\Entity\Projet;
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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: LangageDeProgrammationRepository::class)]
#[ApiResource(
    shortName: 'Module gestion de publication Langage de programmation -Association',
)]

#[GetCollection(
    uriTemplate: '/langage/liste',
    normalizationContext: ['groups' => ['langageDeProgrammation:index']]
)]

#[Get(
    uriTemplate: '/langage/{id}',
    forceEager: true,
    normalizationContext: ['groups' => ['langageDeProgrammation:show']]
)]

#[Post(
    uriTemplate: '/langage/ajouter',
    security: "is_granted('ROLE_ADMIN')",
    denormalizationContext: ['groups' => ['langageDeProgrammation:create']]
)]

#[Put(
    uriTemplate: '/langage/{id}',
    requirements: ['id' => '\d+'],
    securityPostDenormalize: "is_granted('ROLE_ADMIN') and object.isUsedInProjects() == false",
    securityMessage: "Vous n'avez pas les droit requis pour effecuter cette action",
    denormalizationContext: ['groups' => ['langageDeProgrammation:update']]
)]

#[Delete(
    uriTemplate: '/langage/{id}',
    securityPostDenormalize: "is_granted('ROLE_ADMIN') and object.isUsedInProjects() == false",
    securityMessage: "Vous n'avez pas les droit requis pour effecuter cette action",
)]

#[UniqueEntity(
    fields: ['nom'],
    errorPath: 'Lanage de programmation',
    message: 'Cette langage de programmation existe déjà',
)]

class LangageDeProgrammation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['langageDeProgrammation:show', 'langageDeProgrammation:index', 'langageDeProgrammation:update'])]
    private ?int $id = null;

    #[ORM\Column(length: 255,  unique: true, type: 'string')]
    #[Groups(
        [
            'langageDeProgrammation:show', 'langageDeProgrammation:index',
            'langageDeProgrammation:create', 'langageDeProgrammation:update',

            /**
             * @see App\Entity/Apprenant
             * ici lorsque jaffiche un apprenant ayant participé à un projet, 
             * je charge les informations du lanage de programmation au lieu de l'uri
             */
            'apprenant:show'

        ]
    )]
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

    /**
     * Vérifier si le langage est utilisé dans au moins un projet
     */
    public function isUsedInProjects(): bool
    {
        $projets = $this->getProjets();

        foreach ($projets as $projet) {
            if ($projet->getLangageDeProgrammation()->contains($this) && $projet->getStatu() !== 'En attente') {
                return true;
            }
        }
        return false;
    }

    public function preRemove()
    {
        // Vérifier si le langage est utilisé dans un projet
        if ($this->isUsedInProjects()) {
            throw new \Exception('Le langage est utilisé dans un projet et ne peut pas être supprimé.');
        }
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
