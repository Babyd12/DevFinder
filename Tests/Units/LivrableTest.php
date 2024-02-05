<?php

namespace App\Tests\Units;

use App\Entity\Livrable;

use App\Entity\Apprenant;
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

    public function getLienDuLivrable(): void
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

    public function getApprenant(): void
    {
        $apprenant = new Apprenant();
        $apprenants = $this->livrable->getApprenant($apprenant);
        $this->assertInstanceOf(Collection::class, $apprenants);
    }
}
