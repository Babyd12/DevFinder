<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Apprenant;
use App\Entity\Association;
use App\Entity\Entreprise;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Log\LoggerInterface;


class UserStatuSubscriber implements EventSubscriberInterface
{

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setUserRole(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $this->logger->info('addProjetStatu is called.');
      
        if (($entity instanceof Apprenant || $entity instanceof Association || $entity instanceof Entreprise)  &&
            method_exists($entity, 'getRoles') && 
            $entity->getRoles() !== null
        ) {
            $entity->setRoles($entity->getRoles());
        } else {
            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['setUserRole', EventPriorities::PRE_WRITE],

        ];
    }
}
