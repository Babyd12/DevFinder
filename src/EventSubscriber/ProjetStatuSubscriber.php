<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Projet;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ProjetStatuSubscriber implements EventSubscriberInterface
{
    public function addProjetStatu(ViewEvent $event): void
    {
        
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($entity instanceof Projet  && $entity->getStatu() === 'En attente') {
            return;
        } elseif ($entity instanceof Projet && $method === Request::METHOD_POST) {
            // $entity->setStatu('En attente');
        } else {
            return;
        }
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['addProjetStatu', EventPriorities::PRE_WRITE],

        ];
    }
}
