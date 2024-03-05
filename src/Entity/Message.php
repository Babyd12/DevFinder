<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Trait\CommonDateTrait;
use App\Repository\MessageRepository;
use App\State\MessageStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]

#[ApiResource()]

#[Post(
    uriTemplate: 'message/envoyer',
    denormalizationContext: ['groups' => ['message:create']],
    security: "is_granted('ROLE_ASSOCIATION') or  is_granted('ROLE_APPRENANT') ",
    processor: MessageStateProcessor::class,

)]

#[Get(
    uriTemplate: 'message/{id}',
    normalizationContext: ['groups' => ['message:show']],
    securityPostDenormalize: "is_granted('ROLE_APPRENANT') or is_granted('ROLE_ASSOCIATION')",
)]

#[GetCollection(
    uriTemplate: 'message/listes',
    normalizationContext: ['groups' => ['message:index']],

)]
#[Put(
    uriTemplate: 'message/modifier/{id}',

    denormalizationContext: ['groups' => ['message:updateOne']],
)]

#[Delete(
    uriTemplate: 'message/supprimer/{id}',
    denormalizationContext: ['groups' => ['message:delete']],
)]

#[ORM\HasLifecycleCallbacks]
class Message
{
    use CommonDateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('message:create', 'message:index', 'message:updateOne', 'message:delete')]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    #[Groups('message:index', 'message:show')]
    private ?\DateTimeImmutable $updatedAt = null;


    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups('message:create', 'message:index', 'message:updateOne', 'message:delete')]
    private ?Association $association = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups('message:create', 'message:index', 'message:updateOne', 'message:delete')]
    private ?Apprenant $apprenant = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('message:create', 'message:index', 'message:updateOne', 'message:delete')]
    private ?Projet $projet = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

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

    public function getApprenant(): ?Apprenant
    {
        return $this->apprenant;
    }

    public function setApprenant(?Apprenant $apprenant): static
    {
        $this->apprenant = $apprenant;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): static
    {
        $this->projet = $projet;

        return $this;
    }
}
