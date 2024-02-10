<?php

namespace App\State;

use App\Entity\Apprenant;
use App\Entity\Entreprise;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class RemoveUserToRelationProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private ProcessorInterface $processorInterface
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $entity = $this->security->getUser();
        if ($entity instanceof Apprenant) {
            $entity->removeCompetence($data);
            return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        } 
        else if ($entity instanceof Entreprise) {
            try {
                $apprenant = $this->entityManager->getRepository(Apprenant::class)->find($uriVariables['id']);
                if ($apprenant ==  null) {
                    return new JsonResponse(["message" => "Aucun developpeur ne correspond à votre recherche"], Response::HTTP_NOT_FOUND);
                }
                if ($entity->getApprenants()->contains($apprenant)) {
                    $entity->removeApprenant($apprenant);
                    $this->entityManager->flush();

                    return new JsonResponse(["message" => "Vous avez mis fin au recrutement de ce développeur."], Response::HTTP_OK);
                } else {
                    return new JsonResponse(["message" => "Le développeur n'était pas recruté."], Response::HTTP_BAD_REQUEST);
                }
            } catch (\Exception $e) {
                return new JsonResponse(["message" => "Une erreur s'est produite lors de la suppression du recrutement.", "error" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $this->processorInterface->process($data, $operation, $uriVariables, $context);
    }
}
