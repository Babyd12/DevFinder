<?php

namespace App\State;

use App\Entity\Projet;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateFile implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processorInterface,
        private EntityManager $entityManager,
        private Request $request,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        return $this->processorInterface->process($data, $operation, $uriVariables, $context);
    }

    public function updateFileValidator($data, $operation, $uriVariables, $context)
    {
    }
}
