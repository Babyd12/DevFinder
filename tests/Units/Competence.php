<?php

namespace App\Tests\Units;

use App\Entity\Apprenant;
use App\Entity\Competence;
use App\Entity\Livrable;
use App\Entity\Projet;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class CompetenceTest extends TestCase
{
    private $competence;
    protected function setUp(): void
    {
        $this->competence = new Competence();
    }

    
    public function testGetId(): void
    {
        $this->assertNull($this->competence->getId());
    }

    public function testGetTitre(): void
    {
        $titre = 'Titre de test';
        $this->competence->setTitre($titre);

        $this->assertEquals($titre, $this->competence->getTitre());
    }


    public function testGetNom(): void
    {
        $competence = new Competence();
        $competence =$competence->setNom('Php');
        $this->assertEquals('Php',$competence->getNom());
    }

    public function testgetDescription(): void 
    {
        $description = new Competence();
        $description->setDescription('Description');
        $this->assertEquals('Description',$description->getDescription());
    }

    public function testgetApprenant(): void 
    {
        $apprenant = new Apprenant();
        $this->assertInstanceOf(Apprenant::class, $this->competence->getApprenant());
        $this->assertEquals($apprenant, $this->competence->getApprenant());

    }

    public function testSetApprenant()
    {
        $apprenant = new Apprenant(); // Créez une instance de Apprenant si nécessaire
        $this->competence->setApprenant($apprenant);

        $this->assertEquals($apprenant, $this->competence->getApprenant());
    }
}
