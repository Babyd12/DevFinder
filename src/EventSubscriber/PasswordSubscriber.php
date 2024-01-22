<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Apprenant;
use App\Entity\Association;
use App\Entity\Entreprise;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordSubscriber implements EventSubscriberInterface
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        // dd($userPasswordHasherInterface);
    }

    public function hashPassword(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($entity instanceof Apprenant || $entity instanceof Association || $entity instanceof Entreprise)  &&
            in_array($method, [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH]) &&
            method_exists($entity, 'getPassword') && $entity->getPassword() !== null
        ) {
            $hashedPassword = $this->userPasswordHasherInterface->hashPassword($entity, $entity->getPassword());
            $entity->setMotDePasse($hashedPassword);
        } else {
            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE],
        ];
    }
}
