<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Entity\Association;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustumAdminController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }
    #[Route('/api/administrateur/liste/associations', name: 'app_custum_admin_associations')]
    public function listeAssociations(): JsonResponse
    { 
        return $this->json([
            'liste des associations' => $this->entityManager->getRepository(Association::class)->findAll(),
        ]);
    }

    #[Route('/api/administrateur/liste/developpeurs', name: 'app_custum_admin_developpeurs')]
    public function listeApprenants(): JsonResponse
    { 
        return $this->json([
            'liste des developpeurs' =>$this->entityManager->getRepository(Apprenant::class)->findAll(),
        ]);
    }

    #[Route('/api/administrateur/liste/entreprises', name: 'app_custum_admin_entreprises')]
    public function listeEntreprises(): JsonResponse
    { 
        return $this->json([
            'liste des entreprises' =>$this->entityManager->getRepository(Entreprise::class)->findAll(),
        ]);
    }


}
