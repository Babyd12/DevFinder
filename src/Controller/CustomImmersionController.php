<?php

namespace App\Controller;

use Ramsey\Uuid\Uuid;
use App\Entity\Immersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomImmersionController extends AbstractController
{

    #[Route('/api/immersion/editer/cachier_charge/{id}', name: 'app_immersion_editer', methods: ['PATCH', 'POST'])]
    public function editerCahierDeCharge(Request $request, $id, EntityManagerInterface $entityManager)
    {
        $immersion = $entityManager->getRepository(Immersion::class)->find($id);

        if (!$immersion) {
            throw $this->createNotFoundException('Ressource non trouvée');
        }

        $nouveauFichier = $request->files->get('cahierDeCharge');

        if ($nouveauFichier !== null && $nouveauFichier->isValid()) {

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
            $ancienFichier = $this->getParameter('kernel.project_dir') . '/public/fichiers/immersions/' . $immersion->getNomFichier();

            if (file_exists($ancienFichier)) {
                unlink($ancienFichier);
            } else {
                return new JsonResponse(["message" => "Le fichier n'existe pas ou a été temporairement déplacé"], 404);
            }

            $uuid = Uuid::uuid4();
            $nomUniqueDeFichier = $uuid->toString() . '.' . $nouveauFichier->getClientOriginalExtension();

            $destination = $this->getParameter('kernel.project_dir') . '/public/fichiers/immersions/';
            $nouveauFichier->move($destination, $nomUniqueDeFichier);

            // Mettre à jour le nom du fichier dans l'entité Immersion
            $immersion->setNomFichier($nomUniqueDeFichier);

            $entityManager->flush();
            return new JsonResponse(["message" => "Vous avez édité le cahier de charge avec succès"], 200);
        } else {
            return new JsonResponse(["message" => "Veuillez télecharger un fichier ou le fichier est invalide"], 422);
        }
    }
}
