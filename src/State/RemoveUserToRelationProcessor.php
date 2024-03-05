<?php

namespace App\State;

use Exception;
use App\Entity\Apprenant;
use App\Entity\Entreprise;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Services\SendMailService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class RemoveUserToRelationProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private ProcessorInterface $processorInterface,
        private SendMailService $mailer,
    ) {
    }
    
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $entity = $this->security->getUser();
        if ($entity instanceof Apprenant) {

            return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        } else if ($entity instanceof Entreprise) {
            try {
                $apprenant = $this->entityManager->getRepository(Apprenant::class)->find($uriVariables['id']);
                if ($apprenant ==  null) {
                    return new JsonResponse(["message" => "Aucun developpeur ne correspond à votre recherche"], Response::HTTP_NOT_FOUND);
                }
                if ($entity->getApprenants()->contains($apprenant)) {


                    $this->entityManager->beginTransaction();

                    try {
                        $entity->addApprenant($apprenant);
                        
                        $this->mailer->sendEmail(
                            $this->mailer->defaultFrom(),
                            $apprenant->getEmail(),
                            $this->mailer->getSubjectCongedierApprenant(),
                            $this->mailer->getTemplateCongedierApprenant(),
                            ['apprenant' => $apprenant, 'entreprise' => $entity]
                        );
                        
                        // Si tout est réussi, validez la transaction
                        $this->entityManager->commit();
                        $this->entityManager->flush();
                    } catch (Exception $e) {
                        // Si une exception est levée, annulez la transaction
                        $this->entityManager->rollback();
                        return new JsonResponse(['error' => $e->getMessage() ]);
                    }

                    return new JsonResponse(["message" => "Vous avez mis fin au recrutement de ce développeur."], Response::HTTP_OK);
                } else {
                    return new JsonResponse(["message" => "Le développeur n'était pas recruté."], Response::HTTP_BAD_REQUEST);
                }
            } catch (\Exception $e) {
                return new JsonResponse(["message" => "Une erreur s'est produite lors de la suppression du recrutement.", "error" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $this->processorInterface->process($data, $operation, $uriVariables, $context);
    }
}
