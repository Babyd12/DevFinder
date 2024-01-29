<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Administrateur;
use App\Entity\Apprenant;
use App\Entity\Association;
use App\Entity\Entreprise;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SuccesLoginSubscriber implements EventSubscriberInterface
{
    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $content = $event->getResponse()->getContent();
        $user = $event->getUser();
        $data = json_decode($content, true);
        $token = $data['token'];
       
        if ($user instanceof Administrateur || $user instanceof Apprenant  || $user instanceof Association || $user instanceof Entreprise) {
            $user->getEmail();
            $event->setResponse(new JsonResponse([
                'Nom complet' => $user->getNomComplet(),
                'email' => $user->getUserIdentifier(),
                'token' => $token,
            ], JsonResponse::HTTP_OK));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => ['onLoginSuccessEvent', EventPriorities::PRE_RESPOND],
        ];
    }
}
