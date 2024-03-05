<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessageStateProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $processorInterface){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $association = $data->getAssociation();
        $apprenant = $data->getApprenant();
        $projet = $data->getProjet();
      
        if(empty($projet)){
            return new JsonResponse(["error" =>"Veuillez fournir l'uri du projet"], 400);
        }
        if(!empty($apprenant) && !$projet->apprenantIsInProjet($apprenant)){
            return new JsonResponse(["error" =>"Ce apprenant ne participe pas au projet"],422);
        }
        if(!empty($association) && !$projet->isProjectOwner($association)){
            return new JsonResponse(["error" =>"Cette association n'est pas propriétaire de ce prejet"],401);
        }
    
        return $this->processorInterface->process($data, $operation, $uriVariables, $context);
    }
}
