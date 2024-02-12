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
        if ($event instanceof LoginSuccessEvent && $event->getResponse() !== null ) {
            
            $content = $event->getResponse();
            $content->getContent();
            $user = $event->getUser();
            $data = json_decode($content->getContent(), true);
            $token = $data['token'];

            if ($user instanceof Administrateur || $user instanceof Apprenant || $user instanceof Association || $user instanceof Entreprise) {
                $user->getEmail();
                $event->setResponse(new JsonResponse([
                    'nom_complet' => $user->getNomComplet(),
                    'email' => $user->getUserIdentifier(),
                    'role' => $user->getRoles(),
                    'token' => $token,
                    'role' => $user->getRoles(),
                    'id' => $user->getId(),
                ], JsonResponse::HTTP_OK));
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => ['onLoginSuccessEvent', EventPriorities::PRE_RESPOND],
        ];
    }
}
