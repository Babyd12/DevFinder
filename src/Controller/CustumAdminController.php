<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Entity\Entreprise;
use App\Entity\Association;
use App\Entity\Administrateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustumAdminController extends AbstractController
{
    private $security;
    private $entityManager;
    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
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

    #[Route('/api/administrateur/liste/utilisateurs', name: 'app_custum_admin_listeUtilisateur')]
    public function listeEntreprises(): JsonResponse
    { 
        $enptreprises = $this->entityManager->getRepository(Entreprise::class)->findAll();
        $association = $this->entityManager->getRepository(Association::class)->findAll();
        $apprenant = $this->entityManager->getRepository(Apprenant::class)->findAll();

        $liste = array_merge($enptreprises, $apprenant, $association);
        return $this->json([
            'utilisateurs' =>$liste
        ]);
    }

    #[Route('/api/administrateur/liste/utilisateursBloque', name: 'app_custum_admin_listeUtilisateurBloquer')]
    public function listeUtilisateursBloque(): JsonResponse
    { 
        $enptreprises = $this->entityManager->getRepository(Entreprise::class)->findBy(['etat' => true]);
        $association = $this->entityManager->getRepository(Association::class)->findBy(['etat' => true]);
        $apprenant = $this->entityManager->getRepository(Apprenant::class)->findBy(['etat' => true]);

        $liste = array_merge($enptreprises, $apprenant, $association);
        return $this->json([
            'utilisateursBloque' =>$liste
        ]);
    }
}
