<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Apprenant;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomProjetController extends AbstractController
{

    private Security $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
    }

    public function getSecurity(EntityManagerInterface $entityManager, string $id)
    {
        $user = $this->security->getUser();

        if ($user === null) {
            // Gérer le cas où l'utilisateur n'est pas connecté
            return $this->json(['error' => 'User not authenticated'], 401);
        }


        $apprenantLogged  = $user->getUserIdentifier();
        // Récupérer le projet et l'apprenant depuis la base de données
        $projet = $entityManager->getRepository(Projet::class)->find($id);
        $apprenant = $entityManager->getRepository(Apprenant::class)->findOneByEmail($apprenantLogged);

        // Vérifier si le projet et l'apprenant existent
        if (!$projet || !$apprenant) {
            return new JsonResponse(["message" => "Le projet ou l'apprenant n'existe pas"], Response::HTTP_NOT_FOUND);
        }
        return ['apprenant' => $apprenant, 'projet' => $projet];
    }

    #[Route('/api/apprenant/participer/projet/{id}', name: 'participerProjet', methods: ['GET'])]
    public function addApprenantToProject(EntityManagerInterface $entityManager, string $id): JsonResponse
    {

        $security = $this->getSecurity($entityManager, $id);
        if ($security instanceof JsonResponse && $security->getStatusCode() === 401) {
            // L'utilisateur n'est pas authentifié, vous pouvez traiter cela ici si nécessaire
            return $security;
        }

        $projet = $security['projet'];
        $apprenant = $security['apprenant'];

        // Vérifier si l'apprenant est déjà inscrit au projet
        if ($projet->getApprenants()->contains($apprenant)) {
            return new JsonResponse(["message" => "Vous participer déjà à ce projet"], Response::HTTP_CONFLICT);
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
        return new JsonResponse(['message' => 'Vous avez été ajouté au projet avec succès', 'données' => $showData], Response::HTTP_OK);
    }

    #[Route('/api/apprenant/quitter/projet/{id}', name: 'quitterProjet',  methods: ['GET'])]
    public function removeApprenantToProject(EntityManagerInterface $entityManager, string $id)
    {
        $security = $this->getSecurity($entityManager, $id);
        if ($security instanceof JsonResponse && $security->getStatusCode() === 401) {
            // L'utilisateur n'est pas authentifié, vous pouvez traiter cela ici si nécessaire
            return $security;
        }

        $projet = $security['projet'];
        $apprenant = $security['apprenant'];

        // Vérifier si l'apprenant est déjà inscrit au projet
        if (!$projet->getApprenants()->contains($apprenant)) {
            return new JsonResponse(["message" => "Vous tentez de retirer un apprenant innexistant de ce projet"], Response::HTTP_CONFLICT);
        }

        // Ajouter l'apprenant au projet en utilisant la méthode addApprenant
        $projet->removeApprenant($apprenant);
        $entityManager->flush();
        $showData = [
            'Titre' => $projet->getTitre(),
            'Description' => $projet->getDescription(),
            'Nombre de participant' => $projet->getNombreDeParticipant(),
            'Date_limite' => $projet->getDateLimite(),

        ];
        return new JsonResponse(['message' => 'Vous avez été retiré du projet avec succès', 'données' => $showData], Response::HTTP_OK);
    }
}
