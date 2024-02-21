<?php

namespace App\State;

use App\Entity\Apprenant;
use App\Entity\Competence;
use App\Entity\Entreprise;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\DescriptionCompetence;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddUserToRelationProcessor implements ProcessorInterface
{

    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private ProcessorInterface $processorInterface
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $entity = $this->security->getUser();

        if ($entity instanceof Apprenant) {
        
            if($data instanceof DescriptionCompetence){
                $data->setApprenant($entity);
            }

            return $this->processorInterface->process($data, $operation, $uriVariables, $context);
            
        } else if ($entity instanceof Entreprise) {
            
            $apprenant = $this->entityManager->getRepository(Apprenant::class)->find($uriVariables['id']);
            if($apprenant ==  null ){
                return new JsonResponse(["message" => "Aucun developpeur ne correspond à votre recherche"], Response::HTTP_NOT_FOUND);
            }
            if ($entity->getApprenants()->contains($apprenant)) {
                return new JsonResponse(["message" => "Vous avez déjà récruter ce developpeur"], Response::HTTP_CONFLICT);
            }

            $entity->addApprenant($apprenant);
            $this->entityManager->flush();
            return new JsonResponse(["message" => "Vous avez recruter ce developpeur. "], Response::HTTP_OK);
        } else if ($entity instanceof DescriptionCompetence){
            $userLogged = $this->security->getUser();
            dd($data, $operation, $context );
        }
        dd($entity);
       
        return $this->processorInterface->process($data, $operation, $uriVariables, $context);
    }
}

