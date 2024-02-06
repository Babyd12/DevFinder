<?php

namespace App\Tests\Units;

use App\Entity\Projet;

use App\Entity\Apprenant;
use App\Entity\Competence;
use App\Entity\Entreprise;
use App\Entity\Association;
use App\Entity\ProjetStatu;
use PHPUnit\Framework\TestCase;
use App\Entity\LangageDeProgrammation;
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

    public function testgetNombreDeParticipant(): void
    {
        $this->projet->setNombreDeParticipant(30);
        $this->assertEquals('30', $this->projet->getNombreDeParticipant());
    }

    public function setNombreDeParticipant(): void
    {
        $nombreDeParticipant = $this->projet->setNombreDeParticipant(30);
        $this->assertEquals('30', $this->projet->getNombreDeParticipant());
    }

    public function testGetAssociation(): void
    {
        $association = new Association(); 
        $this->projet->setAssociation($association);

        $this->assertInstanceOf(Association::class, $this->projet->getAssociation());
    }

    public function testgetDateLimite(): void
    {
         $this->assertNull($this->projet->getDateLimite());
    }

    public function testSetDateLimite(): void 
    {
        $date = new \DateTime('2024-02-05');
        $this->projet->setDateLimite($date);
        $this->assertInstanceOf(\DateTimeInterface::class, $this->projet->getDateLimite());
        $this->assertEquals($date, $this->projet->getDateLimite());
    }

    public function testSetAssociation(): void
    {
        $association = new Association(); 
        $this->projet->setAssociation($association);
        $this->assertEquals($association, $this->projet->getAssociation());
    }

    public function testGetLangageDeProgrammation(): void
    {
        $langage = new LangageDeProgrammation(); 
        $this->projet->addLangageDeProgrammation($langage);
        $langages = $this->projet->getLangageDeProgrammation();

        $this->assertInstanceOf(Collection::class, $langages);
        $this->assertCount(1, $langages);
        $this->assertTrue($langages->contains($langage));
    }

    public function testAddLangageDeProgrammation(): void
    {
        $langage = new LangageDeProgrammation(); 
        $this->projet->addLangageDeProgrammation($langage);
        $this->assertTrue($this->projet->getLangageDeProgrammation()->contains($langage));
    }

    public function testRemoveLangageDeProgrammation(): void
    {
        $langage = new LangageDeProgrammation(); 
        $this->projet->addLangageDeProgrammation($langage);
        $this->projet->removeLangageDeProgrammation($langage);
        $this->assertFalse($this->projet->getLangageDeProgrammation()->contains($langage));
    }

    public function testGetApprenants(): void
    {
        $apprenant = new Apprenant(); 
        $this->projet->addApprenant($apprenant);

        $apprenants = $this->projet->getApprenants();

        $this->assertInstanceOf(Collection::class, $apprenants);
        $this->assertCount(1, $apprenants);
        $this->assertTrue($apprenants->contains($apprenant));
    }

    public function testAddApprenant(): void
    {
        $apprenant = new Apprenant();
        $this->projet->addApprenant($apprenant);
        $this->assertTrue($this->projet->getApprenants()->contains($apprenant));
    }

    public function testRemoveApprenant(): void
    {
        $apprenant = new Apprenant(); 
        $this->projet->addApprenant($apprenant);
        $this->projet->removeApprenant($apprenant);
        $this->assertFalse($this->projet->getApprenants()->contains($apprenant));
    }

    public function testGetStatu(): void
    {
        $this->projet->setStatu(ProjetStatu::en_attente);
        $this->assertInstanceOf(ProjetStatu::class, $this->projet->getStatu());
    }

    public function testSetStatu(): void
    {  
        $statu = ProjetStatu::en_attente;
        $this->projet->setStatu($statu);
        $this->assertEquals($statu, $this->projet->getStatu());
    }

}