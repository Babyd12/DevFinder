<?php

namespace App\Tests\Units;

use App\Entity\Brief;

use App\Entity\Livrable;
use App\Entity\Apprenant;
use App\Entity\Immersion;
use App\Entity\Competence;
use App\Entity\Entreprise;
use App\Entity\Association;
use App\Entity\LivrableStatu;
use PHPUnit\Framework\TestCase;
use App\Entity\LangageDeProgrammation;
use Doctrine\Common\Collections\Collection;

class LivrableTest extends TestCase
{
    private $livrable;
    protected function setUp(): void
    {
        $this->livrable = new Livrable();
    }

    public function testGetId(): void
    {
        $this->assertNull($this->livrable->getId());
    }

    public function testgetLienDuLivrable(): void
    {
        $lientDuLivrable = 'google.com';
        $this->livrable->setLienDuLivrable($lientDuLivrable);
        $this->assertEquals($lientDuLivrable, $this->livrable->getLienDuLivrable());
    }

    public function testsetLienDuLivrable(): void
    {
        $lienDuLivrable = 'https://dev-t0gt7fxfqc4r13lt.us.auth0.com/api/v2/';
        $this->livrable->setLienDuLivrable($lienDuLivrable);
        $this->assertEquals($lienDuLivrable, $this->livrable->getLienDuLivrable());
    }

    public function testgetApprenant(): void
    {
       $newApprenant = new Apprenant();
       $this->livrable->setApprenant($newApprenant);

       $apprenant = $this->livrable->getApprenant();
       $this->assertInstanceOf(Apprenant::class ,$apprenant);
       $this->assertEquals($apprenant, $newApprenant);

    }

    public function testsetApprenant(): void
    {
        $apprenants = new Apprenant();
        $this->livrable->setApprenant($apprenants);
        $getApprenant = $this->livrable->getApprenant();
        $this->assertInstanceOf(Apprenant::class, $getApprenant) ;
        $this->assertEquals($getApprenant, $this->livrable->getApprenant());
    }

    public function testgetBrief(): void
    {
        $briefs = new Brief();
        $this->livrable->setBrief($briefs);
        $brief = $this->livrable->getBrief();
        $this->assertInstanceOf(Brief::class, $this->livrable->getBrief());
        $this->assertSame($brief, $this->livrable->getBrief());
    }

    public function testsetBrief(): void 
    {
        $briefs = new Brief();
        $this->livrable->setBrief($briefs);

        $isBrief = $this->livrable->getBrief();

        $this->assertInstanceOf(Brief::class, $isBrief);
        $this->assertEquals($isBrief, $this->livrable->getBrief());

    }

    public function testgetImmersion(): void
    {
        $immersions = new Immersion();
        $immersions->setTitre("une belle immersion");

        $this->livrable->setImmersion($immersions);

        $immersion = $this->livrable->getImmersion();
        $this->assertInstanceOf(Immersion::class, $immersion);
        $this->assertEquals($immersion, $this->livrable->getImmersion());
        $this->assertStringContainsString("une belle immersion", $immersion->getTitre());
    }

    public function testSetImmersion(): void
    {
        $immersion = new Immersion();
        $this->livrable->setImmersion($immersion);
        $this->assertInstanceOf(Immersion::class, $this->livrable->getImmersion());
        $this->assertEquals($immersion, $this->livrable->getImmersion());
    }
    
}
