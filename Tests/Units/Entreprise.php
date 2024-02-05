<?php

namespace App\Tests\Units;

use App\Entity\Apprenant;
use App\Entity\Entreprise;
use App\Entity\Projet;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class EntrepriseTest extends TestCase
{
    private $entreprise;
    protected function setUp(): void
    {
        $this->entreprise = new Entreprise();
    }

    public function testGetId(): void
    {
        $this->assertNull($this->entreprise->getId());
    }

    public function testgetNomComplet(): void
    {
        $nom = "Nom de l'Entreprise";
        $this->entreprise->setNomComplet($nom);
        $this->assertEquals($nom, $this->entreprise->getNomComplet());
    }

    public function testGetEmail()
    {
        $email = 'test@example.com';
        $this->entreprise->setEmail($email);

        $this->assertEquals($email, $this->entreprise->getEmail());
    }

    public function testGetMotDePasse()
    {
        $password = 'testPassword';
        $this->entreprise->setMotDePasse($password);

        $this->assertEquals($password, $this->entreprise->getMotDePasse());
    }

    public function testGetRole()
    {
        $this->entreprise->setRoles(['ROLE_ASSOCIATION']);
        $this->assertEquals(['ROLE_ASSOCIATION'], $this->entreprise->getRoles());
    }

    public function testGetDescription(): void
    {
        $this->entreprise->setDescription('description');
        $this->assertEquals('description', $this->entreprise->getDescription());
    }

    public function testgetNumeroIdentificationNaitonal(): void
    {
        $numero_identification_naitonal = "0001462 2G3";
        $this->entreprise->setNumeroIdentificationNaitonal($numero_identification_naitonal);
        $this->assertEquals($numero_identification_naitonal, $this->entreprise->getNumeroIdentificationNaitonal());
    }

    public function testsetTelephone(): void
    {      
        $this->entreprise->setTelephone('708778675');
        $this->assertEquals('708778675', $this->entreprise->getTelephone());
    }

    public function testGetTelephone(): void
    {
        $this->entreprise->setTelephone('786667676');
        $this->entreprise->getTelephone();
        $this->assertEquals('786667676', $this->entreprise->GetTelephone());
    }

    public function tesaddApprenant(): void
    {
        $apprenant = new Apprenant();
        $this->entreprise->addApprenant($apprenant);
        $apprenants = $this->entreprise->getApprenant();
        $this->assertInstanceOf(Collection::class, $apprenants);
        $this->assertCount(1, $apprenants);
        $this->assertTrue($apprenants->contains($apprenant));
    }
    
    public function testgetApprenants(): void 
    {
        $apprenant = new Apprenant();
        $apprenant2 = new Apprenant();
        
        $this->entreprise->addApprenant($apprenant);
        $this->entreprise->addApprenant($apprenant2);

        $apprenants = $this->entreprise->getApprenants();

        $this->assertInstanceOf(Collection::class,$apprenants);
        $this->assertCount(2, $apprenants);
        $this->assertTrue($apprenants->contains($apprenant));
        $this->assertTrue($apprenants->contains($apprenant2));
    }

}
