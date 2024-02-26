<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Projet;
use App\Entity\ProjetStatu;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ProjetStatuSubscriber implements EventSubscriberInterface
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function addProjetStatu(ViewEvent $event): void
    {   
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        
        if ($entity instanceof Projet  && $entity->getStatu() === 'En attente') {
            return;
        } elseif ($entity instanceof Projet && $method === Request::METHOD_POST) {
            $entity->setStatu(ProjetStatu::en_cours);
            $entity->setAssociation($this->security->getUser());
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
