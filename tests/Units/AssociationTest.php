<?php

namespace App\Tests\Units;

use App\Entity\Association;
use App\Entity\Projet;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class AssociationTest extends TestCase
{
    private $association;
    protected function setUp(): void
    {
        $this->association = new Association();
    }

    public function testGetId(): void
    {
        $this->assertNull($this->association->getId());
    }

    public function testgetNomComplet(): void
    {
        $nom = "Nom de l'Association";
        $this->association->setNomComplet($nom);
        $this->assertEquals($nom, $this->association->getNomComplet());
        
    }

    public function testGetEmail()
    {
        $email = 'test@example.com';
        $this->association->setEmail($email);
        $this->assertEquals($email, $this->association->getEmail());
    }

    public function testGetMotDePasse()
    {
        $password = 'testPassword';
        $this->association->setMotDePasse($password);
        $this->assertEquals($password, $this->association->getMotDePasse());
    }

    public function testGetRole()
    {
        $this->association->setRoles(['ROLE_ASSOCIATION']);
        $this->assertEquals(['ROLE_ASSOCIATION'], $this->association->getRoles());
    }

    public function testGetDescription(): void
    {
        $this->association->setDescription('description');
        $this->assertEquals('description', $this->association->getDescription());
    }

    public function testgetNumeroIdentificationNaitonal(): void
    {
        $numero_identification_naitonal = "0001462 2G3";
        $this->association->setNumeroIdentificationNaitonal($numero_identification_naitonal);
        $this->assertEquals($numero_identification_naitonal, $this->association->getNumeroIdentificationNaitonal());
    }


    public function testsetTelephone(): void
    {
        $this->association->setTelephone('708778675');
        $this->assertEquals('708778675', $this->association->getTelephone());
    }

    public function testGetTelephone(): void
    {
        $this->association->setTelephone('786667676');
        $this->association->getTelephone();
        $this->assertEquals('786667676', $this->association->GetTelephone());
    }

    public function testaddProjet(): void
    {
        $projet = new Projet();
        $projet->setTitre('Mise en place d\'une application de gestion de tontine');
        $projet->setNombreDeParticipant(10);

        $projet2 = new Projet();
        $projet2->setTitre('Mise en place de déménagement');

        $this->association->addProjet($projet);
        $this->association->addProjet($projet2);


        $projets = $this->association->getProjets();

        $this->assertInstanceOf(Collection::class, $projets);
        $this->assertCount(2, $projets);
        $this->assertTrue($projets->contains($projet));
        $this->assertTrue($projets->contains($projet2));
        
        // $projetAjoute = $projets->getProjet();


    }

    public function testgetProjets(): void
    {
        $projet = new Projet();
        $this->assertInstanceOf(Collection::class, $this->association->getProjets());
    }

    public function testremoveProjet(): void
    {
        $projet = new Projet();
        $this->association->removeProjet($projet);
        $projets = $this->association->getProjets();
        $this->assertFalse($projets->contains($projet));
    }
}
