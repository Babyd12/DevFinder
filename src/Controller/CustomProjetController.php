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

        // Vérifier si le projet et l'apprenant existent
        if (!$projet || !$apprenant) {
            return new JsonResponse("Le projet ou l'apprenant n'existe pas", Response::HTTP_NOT_FOUND);
        }

        // Vérifier si l'apprenant est déjà inscrit au projet
        if ($projet->getApprenants()->contains($apprenant)) {
            return new JsonResponse("Vous participer déjà à ce projet", Response::HTTP_CONFLICT);
        }

        // Ajouter l'apprenant au projet en utilisant la méthode addApprenant
        $data = $projet->addApprenant($apprenant);
        //   dd($data);
        // Persistez et flush les changements
        $entityManager->flush();
        $showData = [
            'Titre' => $data->getTitre(),
            'Description' => $data->getDescription(),
            'Nombre de participant' => $data->getNombreDeParticipant(),
            'Date_limite' => $data->getDateLimite(),

        ];
        return new JsonResponse(['message' => 'L\'apprenant a été ajouté au projet avec succès', 'données' => $showData], Response::HTTP_OK);
    }

    
}
