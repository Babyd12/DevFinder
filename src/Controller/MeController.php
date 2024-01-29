<?php

namespace App\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MeController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    #[Route('/api/user', name: 'app_me')]
    public function __invoke()
    {
        $user = $this->security->getUser();
        if ($user) {      
            // return new Response((string)$user);
            $userData = [
                'email' => $user->getUserIdentifier(),
                'Nom complet' => (string)$user,
            ];
            // Créez une JsonResponse avec les données formatées en JSON
            return new JsonResponse($userData);
        } else {
            return new JsonResponse(['message' => 'Utilisateur non connecté.']);
        }
    }
}
