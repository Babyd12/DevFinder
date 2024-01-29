<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Apprenant;
use Symfony\Bundle\SecurityBundle\Security;

class RemoveUserToRelationClass implements ProcessorInterface
{
    private $security;
    public function __construct(Security $security, private ProcessorInterface $processorInterface)
    {
        $this->security = $security;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // $entity = $this->security->getUser();
        // if ($entity instanceof Apprenant) {
        //     $entity->removeCompetence($data);
        // }
        return $this->processorInterface->process($data, $operation, $uriVariables, $context);
    }
}
