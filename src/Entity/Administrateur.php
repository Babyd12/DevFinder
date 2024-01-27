<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AdministrateurRepository;
use MessageFormatter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
/*
#[ApiResource(
    // shortName:'Module gestion de publication -Apprenant'
)]

#[GetCollection(
    normalizationContext: [ 'groups' => ['read'] ]
)]

#[Get(
    forceEager: true,
    normalizationContext: [ 'groups' => ['readAll'] ]
)]

#[Post(
    denormalizationContext: [ 'groups' => ['write'] ]
)]

#[Put(

    denormalizationContext: [ 'groups' => ['write:put'] ]
)]
#[Patch(
    denormalizationContext: [ 'groups' => ['write:patch'] ]
)]

#[Delete()]
*/

class Administrateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([ 'read', 'readAll', 'write', 'write:put'])]
    private ?string $nom_complet = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'readAll', 'write', 'write:put'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['write', 'write:put', 'write:patch'])]
    private ?string $mot_de_passe = null;

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
}
