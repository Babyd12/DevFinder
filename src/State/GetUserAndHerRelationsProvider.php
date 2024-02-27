<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

class GetUserAndHerRelationsProvider implements ProviderInterface
{
    public function __construct(private ProviderInterface $providerInterface){}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $descriptionCompetence = $this->providerInterface->provide($operation, $uriVariables, $context);
        // dd($descriptionCompetence);
        return null;
    }
}
