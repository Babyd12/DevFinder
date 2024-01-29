<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Apprenant;

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
        if( $entity instanceof Apprenant )
        {
            return true;
            
        }
        return $this->processorInterface->process($data, $operation, $uriVariables, $context);
    }
}
