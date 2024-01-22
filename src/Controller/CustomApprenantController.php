<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Entity\Projet;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomApprenantController extends AbstractController
{
    // public function __invoke()
    // {
        
    // }
   
    #[Route('/particip/projet', name: 'test')]
    public function index(): Response
    {
        return $this->render('custom_apprenant/index.html.twig', [
            'controller_name' => 'CustomApprenantController',
        ]);
    }

    #[Route('apprenant-{apprenantId}/project-{projetId}', name: 'addApprenantToProjet', methods: ['POST'] )]
    public function addApprenantToProjet( EntityManagerInterface $entityManager, int $apprenantId, int $projetId )
    {
        
        
    }
}
