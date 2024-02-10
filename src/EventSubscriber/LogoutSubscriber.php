<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function onLogoutEvent(LogoutEvent $event): void
    {

        if( ($event->getResponse()->getStatusCode() === JsonResponse::HTTP_OK ) )
        {
            $message = 'Déconnexion réussie.';
            $response = new JsonResponse(['message' => $message], JsonResponse::HTTP_OK);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => ['onLogoutEvent', EventPriorities::POST_RESPOND ]
        ];
    }
}
