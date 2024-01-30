<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Apprenant;
use App\Entity\Competence;
use App\Entity\Entreprise;
use Symfony\Bundle\SecurityBundle\Security;

class SetUserToRelationClass implements ProcessorInterface
{
    private $security;
    public function __construct(Security $security, private ProcessorInterface $processorInterface)
    {
        $this->security = $security;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $entity = $this->security->getUser();
        if( $entity instanceof Apprenant )
        {
            $entity->addCompetence( $data );
            // return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        } 
        // else if( $entity instanceof Entreprise ) {
        //     // dd($data, $operation, $uriVariables, $context);
        // }

        return $this->processorInterface->process($data, $operation, $uriVariables, $context);

    }
}
