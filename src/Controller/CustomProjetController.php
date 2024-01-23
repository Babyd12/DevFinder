<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Apprenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomProjetController extends AbstractController
{



    #[Route('/api/project-{projectId}/apprenant-{apprenantId}', name: 'participateToProject', methods: ['POST'])]
    public function addApprenantToProject(Request $request, EntityManagerInterface $entityManager, string $projectId, string $apprenantId): JsonResponse
    {

        // Récupérer le projet et l'apprenant depuis la base de données
        $projet = $entityManager->getRepository(Projet::class)->find($projectId);
        $apprenant = $entityManager->getRepository(Apprenant::class)->find($apprenantId);

        if (!$projet || !$apprenant) {
            return new JsonResponse(['message' => 'Le projet ou l\'apprenant n\'existe pas'], Response::HTTP_NOT_FOUND);
        }
        // Vérifier si le projet et l'apprenant existent
        if ($projet instanceof Projet &&  $apprenant instanceof Apprenant) {

            // le statut du projet

            if ($projet->getStatu() == !'Non débuté') {
                return new JsonResponse(['message' => 'Vous ne pouvez plus participer à ce projet'], Response::HTTP_NOT_FOUND);

                //l'apprenant a t-il passé l'immersion
            } else if (method_exists($apprenant, 'getImmersion') && $apprenant->getImmersion() === null) {

                return new JsonResponse(['message' => 'Veuillez valider l\'immersion avant.'], Response::HTTP_CONFLICT);

                // Vérifier si l'apprenant est déjà inscrit au projet
            } else if ($projet->getApprenants()->contains($apprenant)) {
                return new JsonResponse(['message' => 'Vous participer déjà à ce projet'], Response::HTTP_CONFLICT);
            }

            // Ajouter l'apprenant au projet en utilisant la méthode addApprenant
            $data = $projet->addApprenant($apprenant);
            $entityManager->flush();
            $showData = [
                'Titre' => $data->getTitre(),
                'Description' => $data->getDescription(),
                'Nombre de participant' => $data->getNombreDeParticipant(),
                'Date_limite' => $data->getDateLimite(),

            ];
            return new JsonResponse(['message' => 'L\'apprenant a été ajouté au projet avec succès', 'données' => $showData], Response::HTTP_OK);
        } else {
            return new JsonResponse(['message' => 'Veuillez fournir un format correcte d\'URI '], Response::HTTP_OK);
        }
    }
}
