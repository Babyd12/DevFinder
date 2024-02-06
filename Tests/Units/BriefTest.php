<?php

namespace App\Tests\Units;

use App\Entity\Apprenant;
use App\Entity\Brief;
use App\Entity\Livrable;
use App\Entity\Projet;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class BriefTest extends TestCase
{
    private $brief;
    protected function setUp(): void
    {
        $this->brief = new Brief();
    }

    public function testGetId()
    {
        $this->assertNull($this->brief->getId());
    }

    public function testGetTitre()
    {
        $titre = 'Titre de test';
        $this->brief->setTitre($titre);

        $this->assertEquals($titre, $this->brief->getTitre());
    }

    public function testGetLienSupport()
    {
        $lienSupport = 'https://google.com';
        $this->brief->setLientSupport($lienSupport);
        $this->assertEquals($lienSupport, $this->brief->getLientSupport());
    }

    public function testGetNiveauDeCompetence()
    {
        $niveauCompetence = 'Avancé';
        $this->brief->setNiveauDeCompetence($niveauCompetence);
        $this->assertEquals($niveauCompetence, $this->brief->getNiveauDeCompetence());
    }
    public function testGetLivrables()
    {
        $livrable = new Livrable(); // Créez une instance de Livrable si nécessaire
        $this->brief->addLivrable($livrable);

        $livrables = $this->brief->getLivrables();

        $this->assertInstanceOf(Collection::class, $livrables);
        $this->assertCount(1, $livrables);
        $this->assertTrue($livrables->contains($livrable));
    }

  


}
