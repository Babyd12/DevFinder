<?php

namespace App\State;

use App\Entity\Apprenant;
use App\Entity\Entreprise;
use App\Entity\Association;
use App\Entity\Administrateur;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetUserLoggedProcessor implements ProcessorInterface
{
    private $security;
    public function __construct(Security $security, private ProcessorInterface $processorInterface)
    {
        $this->security = $security;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $entity = $this->security->getUser();
        if ($entity instanceof Apprenant || $entity instanceof Association || $entity instanceof Entreprise  || $entity instanceof Administrateur ) {

            $userData = [
                'id' => $entity->getId(),
                'email' => $entity->getUserIdentifier(),
                'Nom complet' => $entity->getNomComplet(),
                'role' => $entity->getRoles(),
            ];
            return new JsonResponse($userData);
            // return $this->processorInterface->process($userData, $operation, $uriVariables, $context);
        }
        return new JsonResponse( ['error' => 'Veuillez vous connecter'], JsonResponse::HTTP_FORBIDDEN);
    }
}


// #[Route('/api/user', name: 'app_me')]
// public function __invoke()
// {
//     $user = $this->security->getUser();
//     if ($user) {      
//         // return new Response((string)$user);
//         $userData = [
//             'email' => $user->getUserIdentifier(),
//             'Nom complet' => (string)$user,
//         ];
//         // Créez une JsonResponse avec les données formatées en JSON
//         return new JsonResponse($userData);
//     } else {
//         return new JsonResponse(['message' => 'Utilisateur non connecté.']);
//     }
// }
