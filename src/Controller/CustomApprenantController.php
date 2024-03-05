<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Apprenant;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomApprenantController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
    }

    
    public function getSecurity(EntityManagerInterface $entityManager, string $id)
    {
        $user = $this->security->getUser();

        if ($user === null) {
            // Gérer le cas où l'utilisateur n'est pas connecté
            return $this->json(['error' => 'Veuillez vous connecter '], 401);
        }

        $apprenantLogged  = $user->getUserIdentifier();
        // Récupérer le projet et l'apprenant depuis la base de données
        $projet = $entityManager->getRepository(Projet::class)->find($id);
        $apprenant = $entityManager->getRepository(Apprenant::class)->findOneByEmail($apprenantLogged);

        // Vérifier si le projet et l'apprenant existent
        if (!$projet || !$apprenant) {
            return new JsonResponse(["message" => "Le projet ou l'apprenant n'existe pas"], Response::HTTP_NOT_FOUND);
        }
        return ['apprenant' => $apprenant, 'projet' => $projet];
    }


    /**
     * @see getSecurity
     */
    #[Route('/api/apprenant/projets', name: 'mesProjets', methods: ['GET', 'POST'] )]
    public function mesProjets( EntityManagerInterface $entityManager)
    {
       
        $user = $this->security->getUser();

        if ($user === null) {
            // Gérer le cas où l'utilisateur n'est pas connecté
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        $apprenantLogged  = $user->getUserIdentifier();
        $apprenant = $entityManager->getRepository(Apprenant::class)->findOneByEmail($apprenantLogged);
        $apprenantProjets = $apprenant->getProjet() ;
        
        // dd($apprenant->getPojet());
        return new JsonResponse( $apprenant->getProjet());

    }

   
}
