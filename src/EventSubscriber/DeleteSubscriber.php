<?php

namespace App\EventSubscriber;

use App\Entity\Apprenant;
use App\Entity\Entreprise;
use App\Entity\Association;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteSubscriber implements EventSubscriberInterface
{
    public function showDeleteMessage(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        
        dd('dlelte');
        if ($entity instanceof Apprenant || $entity instanceof Association || $entity instanceof Entreprise  ||
            in_array($method, [Request::METHOD_DELETE]) ) {
            $message = 'La ressource a été supprimée avec succès.';
            $response = new JsonResponse(['message' => $message], JsonResponse::HTTP_OK);
            $event->setResponse($response);
        } else {
            dd('no delete');
        }
    
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['showDeleteMessage', EventPriorities::POST_WRITE],


        ];
    }
}
