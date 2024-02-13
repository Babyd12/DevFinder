<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class LogoutController extends AbstractController
{
    #[Route('/api/deconexion', name: 'app_logout', methods:['POST'])]
    public function logout(AuthenticationUtils $authenticationUtils): never
    {
        // $authenticationUtils->get('security.token_storage')->setAuthenticated(false);
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
        // return new Response('', Response::HTTP_NO_CONTENT);
    }
}
