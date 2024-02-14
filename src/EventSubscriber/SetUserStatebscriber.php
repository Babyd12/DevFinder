<?php

namespace App\EventSubscriber;

use App\Entity\Apprenant;
use App\Entity\Entreprise;
use App\Entity\Association;
use Psr\Log\LoggerInterface;
use App\Entity\Administrateur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class SetUserStatebscriber implements EventSubscriberInterface
{

    public function setUserState(ViewEvent $event) : void 
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        
        if ( ($entity instanceof Apprenant || $entity instanceof Association || $entity instanceof Entreprise )  &&
            in_array($method, [Request::METHOD_POST]) &&
            method_exists($entity, 'getPassword') && $entity->getPassword() !== null
        ) {
            $entity->setEtat(false);
        } else {
            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['setUserState', EventPriorities::PRE_WRITE]

        ];
    }
}
