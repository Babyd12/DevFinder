<?php

namespace App\Security;
use \Symfony\Bundle\SecurityBundle\Security ;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AccessTokenHandler  extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function votreAction(): Response
    {
        // Récupérer l'utilisateur connecté depuis le TokenStorage
        $user = $this->security->getUser();
        dd($user);
        // Utilisez $user comme nécessaire
        // Par exemple, $user->getUsername() pour obtenir le nom d'utilisateur

        return $this->json(['username' => $user->getUserIdentifier()]);
    }
}
