<?php

namespace App\Tests\Units;

use App\Entity\Apprenant;
use App\Entity\Immersion;
use App\Entity\Livrable;
use App\Entity\Projet;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class ImmersionTest extends TestCase
{
    private $immersion;
    protected function setUp(): void
    {
        $this->immersion = new Immersion();
    }

    
    public function testGetId(): void
    {
        $this->assertNull($this->immersion->getId());
    }

    public function testGetTitre(): void
    {
        $titre = 'Titre de test';
        $this->immersion->setTitre($titre);

        $this->assertEquals($titre, $this->immersion->getTitre());
    }

    public function testGetLienSupport(): void
    {
        $lienSupport = 'https://google.com';
        $this->immersion->setLientSupport($lienSupport);
        $this->assertEquals($lienSupport, $this->immersion->getLientSupport());
    }

    public function testsetLienSupport(): void
    {
        $this->immersion->setLienSupport('google.com');
        $this->assertEquals('google.com', $this->immersion->getLienSupport());
    }

    public function testGetLivrables(): void
    {
        $livrable = new Livrable(); 
        $this->immersion->addLivrable($livrable);

        $livrables = $this->immersion->getLivrables();

        $this->assertInstanceOf(Collection::class, $livrables);
        $this->assertCount(1, $livrables);
        $this->assertTrue($livrables->contains($livrable));
    }

    public function testremoveLivrable(): void
    {
        //j'instancie un livrable
        $livrable  = new Livrable();

        //j'ajoute le livrable en pasant par l'immersion
        $this->immersion->addLivrable($livrable);

        //je récupère le livrable que j'ai ajouté
        $livrables = $this->immersion->getLivrables();

        //je test si c'est bien une collenction itérable de livrables
        $this->assertInstanceOf(Collection::class, $livrables);
        $this->assertCount(1, $livrables);

        //je m'assure que le livrable est bien contenu dans la collection que j'ai recupéré
        $this->assertTrue($livrables->contains($livrable));

        //je supprime le livrable que j'ai ajouté
        $livrables->remove($livrable);

        //je vérifie qu'il est bien supprimé
        $this->assertFalse($livrables->contains($livrable));
    }

}
