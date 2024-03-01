<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

class GetBriefRelations implements ProviderInterface
{
    public function __construct(private ProviderInterface $providerInterface)
    {
        
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $brief = $this->providerInterface->provide($operation, $uriVariables, $context);
        return null;
        // Retrieve the state from somewhere
    }
}
