<?php

namespace App\State;

use Exception;
use App\Entity\Apprenant;
use App\Entity\Competence;
use App\Entity\Entreprise;
use ApiPlatform\Metadata\Operation;
use App\Entity\DescriptionCompetence;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Services\SendMailService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddUserToRelationProcessor implements ProcessorInterface
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

            if ($data instanceof DescriptionCompetence) {
                $data->setApprenant($entity);
            }

            return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        } else if ($entity instanceof Entreprise) {
            
            $apprenant = $this->entityManager->getRepository(Apprenant::class)->find($uriVariables['id']);
            if ($apprenant ==  null) {
                return new JsonResponse(["message" => "Aucun developpeur ne correspond à votre recherche"], Response::HTTP_NOT_FOUND);
            }
            if ($entity->getApprenants()->contains($apprenant)) {
                return new JsonResponse(["message" => "Vous avez déjà récruter ce developpeur"], Response::HTTP_CONFLICT);
            }

            $this->entityManager->beginTransaction();

            try {
                $entity->addApprenant($apprenant);
                $this->entityManager->flush();

                $this->mailer->sendEmail(
                    $this->mailer->defaultFrom(),
                    $apprenant->getEmail(),
                    $this->mailer->getSubjectRecrutementApprenant(),
                    $this->mailer->getTemplateRecrutementEntreprise(),
                    ['apprenant' => $apprenant, 'entreprise' => $entity]
                );

                // Si tout est réussi, validez la transaction
                $this->entityManager->commit();
            } catch (Exception $e) {
                // Si une exception est levée, annulez la transaction
                $this->entityManager->rollback();
                return new JsonResponse(['error' => $e->getMessage() ]);
            }

            return new JsonResponse(["message" => "Vous avez recruter ce developpeur. "], Response::HTTP_OK);
        }

        return $this->processorInterface->process($data, $operation, $uriVariables, $context);
    }
}
