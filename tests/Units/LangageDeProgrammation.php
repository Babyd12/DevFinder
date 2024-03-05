<?php

namespace App\Tests\Units;

use App\Entity\Projet;
use App\Entity\Livrable;
use App\Entity\Apprenant;
use App\Entity\ProjetStatu;
use PHPUnit\Framework\TestCase;
use App\Entity\LangageDeProgrammation;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Api\QueryParameterValidator\Validator\Enum;

class LangageDeProgrammationTest extends TestCase
{
    private $langageDeProgrammation;
    protected function setUp(): void
    {
        $this->langageDeProgrammation = new LangageDeProgrammation();
    }


    public function testGetId(): void
    {
        $this->assertNull($this->langageDeProgrammation->getId());
    }

    public function testGetTitre(): void
    {
        $titre = 'Titre de test';
        $this->langageDeProgrammation->setTitre($titre);

        $this->assertEquals($titre, $this->langageDeProgrammation->getTitre());
    }

    public function testGetLienSupport(): void
    {
        $lienSupport = 'https://google.com';
        $this->langageDeProgrammation->setLientSupport($lienSupport);
        $this->assertEquals($lienSupport, $this->langageDeProgrammation->getLientSupport());
    }
    
    public function testgetProjets(): void
    {
        $projet =  new Projet();
        $projet2 = new Projet();
        $projet3 = new Projet();
        $projets = $this->langageDeProgrammation->getProjets($projet);
        $this->assertInstanceOf($projets, $this->langageDeProgrammation->getProjet());
        $this->assertCount(3, $projets);

    }

    public function testaddProjet(): void
    {
        $projet = new Projet();
        $this->langageDeProgrammation->addProjet($projet);
        $this->assertInstanceOf(Projet::class, $projet);
    }

    public function testremoveProjet(): void
    {
        $projet = new Projet();
        $projets = $this->langageDeProgrammation->addProjet($projet);

        $this->assertInstanceOf(Collection::class, $projets);
        $this->assertTrue($projets->contains($projet));

        $this->langageDeProgrammation->removeProjet($projet);
        $this->assertFalse($projets->contains($projet));
    

    }

    public function testisUsedInProjects(): void
    {
        $projet = new Projet();
        $projets = $this->langageDeProgrammation->getProjets();
        $this->assertTrue($this->langageDeProgrammation->isUsedInProjects());

        $projet->setStatu(ProjetStatu::en_cours );
        $this->assertFalse($this->langageDeProgrammation->isUsedInProjects());

    }

    public function testpreRemove(): void 
    {
        $projet = new Projet();
        
        //je test qu'un langage nest contenu dans aucun projet terminé ou en cours
        $canRemove = $this->langageDeProgrammation->preRemove();
        $this->assertTrue($canRemove);

        //j'ajoute un projet en attente pour simuler le retour du message d'erreur
        $projet->setStatu(ProjetStatu::en_cours );
        $this->assertFalse($canRemove);

    }

    

}
