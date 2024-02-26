<?php

namespace App\State;

use App\Entity\Projet;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjetUpdateFile implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processorInterface,
        private EntityManager $entityManager,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof Projet && $operation instanceof Post && !empty($uriVariables ) ) {
                $projet = $this->entityManager->getRepository(Projet::class)->find($uriVariables['id']);
                $ancienCahierDecharge = $projet->getCahierDecharge();

                // $projet->setCahierDecharge($data->getCahierDecharge());
                
                dd($data, $projet);

                
                return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        } 
        else {
            return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        }
       
    }
    
}
