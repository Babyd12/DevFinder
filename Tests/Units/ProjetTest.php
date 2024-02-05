<?php

namespace App\Tests\Units;

use App\Entity\Projet;

use App\Entity\Competence;
use App\Entity\Entreprise;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;

class ProjetTest extends TestCase
{
    private $projet;
    protected function setUp(): void
    {
        $this->projet = new Projet();
    }

    public function testGetId(): void
    {
        $this->assertNull($this->projet->getId());
    }

    public function testsgetTitre() : void
    {
        $nom = "Nom du projet";
        $this->projet->setTitre($nom);
        $this->assertEquals($nom, $this->projet->getTitre());
    }

    public function testgetDescription(): void 
    {
        $this->projet->setDescription('description');
        $this->assertEquals('description', $this->projet->getDescription());
    }

    public function testgetNombreDeParticipant()
    {
        $this->projet->setNombreDeParticipant(30);
        $this->assertEquals('30', $this->projet->getNombreDeParticipant());
    }

}