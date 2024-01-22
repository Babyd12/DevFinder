<?php 

namespace App\Controller;

use App\Entity\Apprenant;
use App\Entity\Projet;
use Doctrine\ORM\EntityManagerInterface;
use symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomProjectHandler extends AbstractController
{
    public function addApprenantToProjectHandler(EntityManagerInterface $entityManager, int $projectId, $apprenantId )
    {
        $projet = $entityManager->getRepository(Projet::class)->find($projectId);
        $apprenant = $entityManager->getRepository(Apprenant::class)->find($apprenantId);
    
        // Ajouter l'apprenant au projet en utilisant la méthode addApprenant
        $projet->addApprenant($apprenant);
    
        // Persistez et flush les changements
        $entityManager->flush();
        return $entityManager;
    }



}