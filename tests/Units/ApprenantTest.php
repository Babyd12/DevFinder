<?php

namespace App\Tests\Units;

use App\Entity\Projet;
use App\Entity\Apprenant;
use App\Entity\Competence;
use App\Entity\Entreprise;
use PHPUnit\Framework\TestCase;
use App\Entity\DescriptionCompetence;
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

    public function testgetNomComplet(): void
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

  
    public function testAddDescriptionCompetence()
    {
        // Créer une instance de Apprenant
        $apprenant = new Apprenant();

        // Créer une instance de DescriptionCompetence
        $descriptionCompetence = new DescriptionCompetence();

        // Appeler la méthode addDescriptionCompetence
        $result = $apprenant->addDescriptionCompetence($descriptionCompetence);

        // Vérifier que la méthode renvoie $this
        $this->assertSame($apprenant, $result);

        // Vérifier que la collection contient $descriptionCompetence
        $this->assertTrue($apprenant->getDescriptionCompetences()->contains($descriptionCompetence));

        // Vérifier que $descriptionCompetence a été correctement associé à $apprenant
        $this->assertSame($apprenant, $descriptionCompetence->getApprenant());
    }

    public function testRemoveDescriptionCompetence()
    {
        // Créez une instance de l'entité Apprenant
        $apprenant = new Apprenant();

        // Créez une instance de l'entité DescriptionCompetence
        $descriptionCompetence = new DescriptionCompetence();

        // Ajoutez la DescriptionCompetence à l'Apprenant
        $apprenant->addDescriptionCompetence($descriptionCompetence);

        // Assurez-vous que la DescriptionCompetence a été ajoutée
        $this->assertCount(1, $apprenant->getDescriptionCompetences());

        // Appelez la méthode removeDescriptionCompetence pour supprimer la DescriptionCompetence
        $apprenant->removeDescriptionCompetence($descriptionCompetence);

        // Assurez-vous que la DescriptionCompetence a été supprimée
        $this->assertCount(0, $apprenant->getDescriptionCompetences());

        // Assurez-vous que la relation a été correctement mise à jour
        $this->assertNull($descriptionCompetence->getApprenant());
    }

    public function testGetEntreprise(): void
    {
        $entreprise = new Entreprise();
        $this->assertInstanceOf(Collection::class, $this->apprenant->getEntreprises());
    }
}
