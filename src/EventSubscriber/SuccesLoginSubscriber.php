<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Administrateur;
use App\Entity\Apprenant;
use App\Entity\Association;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SuccesLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager){}

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        if ($event instanceof LoginSuccessEvent && $event->getResponse() !== null) {

            $content = $event->getResponse();
            $content->getContent();
            $user = $event->getUser();
            $data = json_decode($content->getContent(), true);
            $token = $data['token'];
            if (
                (
                    $user instanceof Administrateur || $user instanceof Apprenant
                    || $user instanceof Association || $user instanceof Entreprise
                )
                && !$user->isEtat()
            ) {
                
                if($user->getRoles() !== 'ROLE_ADMIN'){

                }
                $event->setResponse(new JsonResponse([
                    'nom_complet' => $user->getNomComplet(),
                    'email' => $user->getUserIdentifier(),
                    'role' => $user->getRoles(),
                    'id' => $user->getId(),
                    'role' => $user->getRoles(),
                    'telephone' => $user->getRoles() == 'ROLE_ADMIN' ? $user->getTelephone() : '',
                    'description' => $user->getRoles() == 'ROLE_ADMIN' ? $user->getDescription() : '',
                    'token' => $token,
                ], JsonResponse::HTTP_OK));
                
            } else {
                $event->setResponse(new JsonResponse([
                    'message' => 'Votre compte est temporairement suspendu. Veuillez conctacter le support technique'
                ]));
            }
        }
    }

    // public function isUserExist(LoginSuccessEvent $event): void
    // {
    //     $user = $event->getUser();
    //     $cpt = 0;
    //     if ($event instanceof LoginSuccessEvent && $event->getResponse() !== null) {
    //         if ($user instanceof Administrateur && $this->entityManager->getRepository(Administrateur::class)->findOneBy(['email', $user->getEmail()]) !== null) {
    //             $cpt++;
    //         }

    //         if ($user instanceof Association && $this->entityManager->getRepository(Association::class)->findOneBy(['email', $user->getEmail()]) !== null) {
    //             $cpt++;
    //         }

    //         if ($user instanceof Entreprise && $this->entityManager->getRepository(Entreprise::class)->findOneBy(['email', $user->getEmail()]) !== null) {
    //             $cpt++;
    //         }

    //         if ($user instanceof Apprenant && $this->entityManager->getRepository(Entreprise::class)->findOneBy(['email', $user->getEmail()]) !== null) {
    //             $cpt++;
    //         }
    //     }
    // }
    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => [
                'onLoginSuccessEvent', EventPriorities::PRE_RESPOND,
                'isLockedUser', EventPriorities::PRE_RESPOND,
            ],
        ];
    }
}
