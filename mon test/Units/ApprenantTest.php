<?php

namespace App\Tests\Units;

use App\Entity\Projet;
use App\Entity\Apprenant;
use App\Entity\Competence;
use App\Entity\Entreprise;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;

class ApprenantTest extends TestCase
{
    private $apprenant;
    protected function setUp(): void
    {
        $this->apprenant = new Apprenant();
    }

    public function testGetId(): void
    {
        $this->assertNull($this->apprenant->getId());
    }

    public function testgetNomComplet() : void
    {
        $nom = "Nom de l'apprenant";
        $this->apprenant->setNomComplet($nom);
        $this->assertEquals($nom, $this->apprenant->getNomComplet());
    }

    public function testGetEmail()
    {
        $email = 'test@example.com';
        $this->apprenant->setEmail($email);

        $this->assertEquals($email, $this->apprenant->getEmail());
    }

    public function testGetMotDePasse()
    {
        $password = 'testPassword';
        $this->apprenant->setMotDePasse($password);

        $this->assertEquals($password, $this->apprenant->getMotDePasse());
    }

    public function testGetRole()
    {
        $this->apprenant->setRoles(['ROLE_APPRENANT']);

        $this->assertEquals(['ROLE_APPRENANT'], $this->apprenant->getRoles());
    }

    public function testGetProjet()
    {
        $projet = new Projet(); 
        $this->apprenant->addProjet($projet);
        $this->assertInstanceOf(Collection::class, $this->apprenant->getProjet());
        $this->assertTrue($this->apprenant->getProjet()->contains($projet));
    }

    public function testsetTelephone(): void
    {      
        $this->apprenant->setTelephone('708778675');
        $this->assertEquals('708778675', $this->apprenant->getTelephone());
    }

    public function testGetTelephone(): void
    {
        $this->apprenant->setTelephone('786667676');
        $this->apprenant->getTelephone();
        $this->assertEquals('786667676', $this->apprenant->GetTelephone());
    }

    public function testaddCompetence(): void
    {
        $competence = new Competence();
        $competence->setNom('Php');
        $competence->setDescription('Developpement d\'une api rest laravel');
        $competences = $this->apprenant->getCompetences();

        $this->apprenant->addCompetence($competence);
        $this->assertInstanceOf(Collection::class, $competences);
        $this->assertCount(1, $competences);
        
    }
    public function testsetCompetence(): void
    {
        $competence = new Competence();
        $competence->setNom('Php');
        $competence->setDescription('Developpement d\'une api rest laravel');

        $competences = $this->apprenant->getCompetences();

        $this->apprenant->addCompetence($competence);
        $this->assertInstanceOf(Collection::class, $competences);
        $this->assertCount(1, $competences);

        $competenceAjoute = $competences->first();
        $this->assertInstanceOf(Competence::class, $competenceAjoute);
        $this->assertEquals('Php', $competenceAjoute->getNom());

    }

    public function testGetEntreprise(): void
    {
        $entreprise = new Entreprise();
        $this->assertInstanceOf(Collection::class, $this->apprenant->getEntreprises() );
    }

}
