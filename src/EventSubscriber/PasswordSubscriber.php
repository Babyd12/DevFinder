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
use App\Entity\Administrateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasherInterface,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function hashPassword(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($entity instanceof Apprenant || $entity instanceof Association || $entity instanceof Entreprise || $entity instanceof Administrateur)  &&
            in_array($method, [Request::METHOD_POST, Request::METHOD_PATCH]) &&
            method_exists($entity, 'getPassword') && $entity->getPassword() !== null
        ) {
            // dd($entity->getEmail(), $entity->getPassword()) ;    
            $plainPassword = $entity->getPassword();

            // Utiliser needsRehash pour déterminer si le mot de passe nécessite un rehachage
            if (!$this->userPasswordHasherInterface->needsRehash($entity)) {
                // Le mot de passe est déjà haché avec l'algorithme actue
                return;
            } else {
                //si le mot de passe n'est pas hasher alors il s'agit d'une inscription
                if ($this->isNotUniqueEmailInDatabase($entity)) {
                    $response = new JsonResponse(['message' => 'Cette adresse email existe déjà']);
                    $event->setResponse($response);
                    return;
                }
                $hashedPassword = $this->userPasswordHasherInterface->hashPassword($entity, $plainPassword);
                $entity->setMotDePasse($hashedPassword);
            }
        } else {
            return;
        }
    }

    public function isNotUniqueEmailInDatabase($entity): bool
    {
        $existingApprenant = $this->entityManager->getRepository(Apprenant::class)->findOneBy(['email' => $entity->getEmail()]);
        $existingAssociation = $this->entityManager->getRepository(Association::class)->findOneBy(['email' => $entity->getEmail()]);
        $existingEntreprise = $this->entityManager->getRepository(Entreprise::class)->findOneBy(['email' => $entity->getEmail()]);

        if ($existingApprenant == null  && $existingAssociation == null && $existingEntreprise == null) {
            return false;
        
        } else {
            return true;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE],
        ];
    }
}
