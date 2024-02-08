<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Entity\Entreprise;
use App\Entity\Association;
use App\Entity\Administrateur;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustumAdminController extends AbstractController
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/api/utilisateur/connecte', name: 'app_admin_recuperer_utilisateur_connecter')]
    public function index(): Response
    {
        $entity = $this->security->getUser();
        if ($entity instanceof Apprenant || $entity instanceof Association || $entity instanceof Entreprise  || $entity instanceof Administrateur) {

            $userData = [
                'id' => $entity->getId(),
                'email' => $entity->getUserIdentifier(),
                'nom_complet' => $entity->getNomComplet(),
                'role' => $entity->getRoles(),
            ];
            return new JsonResponse($userData, 200);
        }
    }
}
