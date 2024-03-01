<?php

namespace App\Controller;

use Ramsey\Uuid\Uuid;
use App\Entity\Projet;
use App\Entity\Apprenant;
use phpDocumentor\Reflection\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
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

    public function getUserLogged(EntityManagerInterface $entityManager, string $id)
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
            return new JsonResponse(["message" => "Le projet ou l'apprenant n'existe pas ou n'est pas connecté"], Response::HTTP_NOT_FOUND);
        }
        return ['apprenant' => $apprenant, 'projet' => $projet];
    }


    /**
     * @see UserLogged 
     */
    #[Route('/api/apprenant/participer/projet/{id}', name: 'participerProjet', methods: ['GET'])]
    public function addApprenantToProject(EntityManagerInterface $entityManager, string $id): JsonResponse
    {

        $security = $this->getUserLogged($entityManager, $id);
        if ($security instanceof JsonResponse) {
            $securityCode = $security->getStatusCode();
            switch ($securityCode) {
                case 404:
                    return $security;
                    break;
                case 401:
                    // L'utilisateur n'est pas authentifié, vous pouvez traiter cela ici si nécessaire
                    return $security;
                    break;
            }
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
            'cachier_de_charge' => $data->getNomFichier(),
            'Nombre de participant' => $data->getNombreDeParticipant(),
            'Date_limite' => $data->getDateLimite(),
        ];
        return new JsonResponse(['message' => 'Vous avez été ajouté au projet avec succès', 'données' => $showData], Response::HTTP_OK);
    }

    #[Route('/api/apprenant/quitter/projet/{id}', name: 'quitterProjet',  methods: ['GET'])]
    public function removeApprenantToProject(EntityManagerInterface $entityManager, string $id)
    {
        $security = $this->getUserLogged($entityManager, $id);
        if ($security instanceof JsonResponse && $security->getStatusCode() === 401) {
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
            'cachier_de_charge' => $projet->getNomFichier(),
            'Nombre de participant' => $projet->getNombreDeParticipant(),
            'Date_limite' => $projet->getDateLimite(),
        ];
        return new JsonResponse(['message' => 'Vous avez été retiré du projet avec succès', 'données' => $showData], Response::HTTP_OK);
    }

    #[Route('/api/projet/{id}', name: 'app_projet_editer',  methods: ['PATCH', 'POST'])]
    public function editerCahierDeCharge(Request $request, $id, EntityManagerInterface $entityManager)
    {
        $projet = $entityManager->getRepository(Projet::class)->find($id);
        // $file = $request->files->get('CahierDecharge');


        if (!$projet) {
            throw $this->createNotFoundException('Projet non trouvé');
        }


        $nouveauFichier = $request->files->get('CahierDecharge');
        if ($nouveauFichier) {

            $extension = $nouveauFichier->getClientOriginalExtension();

            // Définir les extensions autorisées
            $extensionsAutorisees = ['pdf', 'docx'];

            // Vérifier si l'extension est autorisée
            if (!in_array(strtolower($extension), $extensionsAutorisees)) {
                return new JsonResponse([
                    "message" => "L'extension du fichier n'est pas autorisée. veuillez chargé uniqument",
                    "extension autorise" =>  $extensionsAutorisees,
                ], 400);
            }
            // Supprimer l'ancien fichier s'il existe
            $ancienFichier = $this->getParameter('kernel.project_dir') . '/public/fichiers/projets/' . $projet->getNomFichier();
            if (file_exists($ancienFichier)) {
                unlink($ancienFichier);
            } else {
                return new JsonResponse(["message" => "Lefichier n'existe pas ou à été temporairement déplacé"], 404);
            }
        }

        $uuid = Uuid::uuid4();
        $nomUniqueDeFichier = $uuid->toString() . '.' . $nouveauFichier->getClientOriginalExtension();

        $destination = $this->getParameter('kernel.project_dir') . '/public/fichiers/projets/';
        $nouveauFichier->move($destination, $nomUniqueDeFichier);

        // Mettre à jour le nom du fichier dans l'entité Projet
        $projet->setNomFichier($nomUniqueDeFichier);
        //   upload_tmp_dir = "C:\xampp\tmp"

        // Mettre à jour la taille du fichier si nécessaire
        //   $projet->setImageSize($nouveauFichier->getSize());


        $entityManager->flush();
        return new JsonResponse(["message" => "Vous avez éditer le cahier de charge avec succès"], 200);
        // dd($nouveauFichier);
    }
}
