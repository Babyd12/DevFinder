<?php

namespace App\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Projet;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjetStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processorInterface,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        // dd('hey');
        // dd($data, $operation, $context);
        if ($data instanceof Projet) {
            if ($operation instanceof Post) {
                if ($data->getCahierDecharge() == null) {
                    return new JsonResponse(['error' => 'Veuilelz fournir un cahier de charge '], 403);
                }
                
                return $this->processorInterface->process($data, $operation, $uriVariables, $context);
                
            } else if ($data instanceof Projet && $operation instanceof Patch) {
                $uriTemaplate = $operation->getUriTemplate();
                switch ($uriTemaplate) {
                    case '/apprenant/soumettre/livrableProjet/{id}':
                        return $this->validatorPatchEditerCahierDeCharge($data, $operation, $uriTemaplate, $context);
                        break;
                }

                // return $this->processorInterface->process($data, $operation, $uriVariables, $context);
            }
        } else {
            return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        }
    }


    public function Validator($data, $operation, $uriVariables, $context)
    {
        if ($data instanceof Projet && $operation->getMethod() == 'PUT') {

            if (empty($data->getLienDuRepertoireDistant())) {
                return new JsonResponse(['error' => 'Le champ lien_du_repertoire_distant ne peut pas être vide.'], 403);
            }
        }
        die('good');
    }

    public function validatorPatchSoumttreLivrableProjet($data, $operation, $uriVariables, $context)
    {


        if ($data instanceof Projet && $operation->getMethod() == 'PUT') {

            if (empty($data->getLienDuRepertoireDistant())) {
                return new JsonResponse(['error' => 'Le champ lien_du_repertoire_distant ne peut pas être vide.'], 403);
            }
        }
    }
    public function validatorPatchEditerCahierDeCharge($data, $operation, $uriVariables, $context)
    {

        if ($data->getCahierDecharge() == null) {
            return new JsonResponse(['error' => 'Veuillez fournir un cachier de charge'], 403);
        }

        dd('enregistré le cachier de charge');
    }
}
