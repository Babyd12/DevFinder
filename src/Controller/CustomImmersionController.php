<?php

namespace App\Controller;

use Ramsey\Uuid\Uuid;
use App\Entity\Immersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Regex;

class CustomImmersionController extends AbstractController
{

    #[Route('/api/immersion/editer/cachier_charge/{id}', name: 'app_immersion_editer', methods: ['PATCH', 'POST'])]
    public function editerCahierDeCharge(Request $request, $id, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $immersion = $entityManager->getRepository(Immersion::class)->find($id);

        if ($immersion == null) {
            throw $this->createNotFoundException('Ressource non trouvée');
        }

        $titre = $request->get('titre');
        $lien = $request->get('lien_support');
        $nouveauFichier = $request->files->get('cahierDeCharge');
        if (empty($titre) || empty($lien) || empty($nouveauFichier)) {
            return new JsonResponse([
                "message" => "Veuillez remplir les champs vides",
                "Champs requis" => [
                    $titre ? '' : 'titre',
                    $lien ? '' : 'lien_support',
                    $nouveauFichier ? '' : 'cahierDeCharge',

                ]
            ], 422);
        }

        $validator->validate($lien, new Url());
        $violations =  $validator->validate( 
            $titre,
           [
          
            new Assert\Regex(
                "/^(?!\s*$)(?![0-9]+$)(?![^a-zA-Z0-9À-ÿ\s]+$)[a-zA-Z0-9À-ÿ'\s]*$/",
                message: "La valeur {{ value }}ne peut pas être vide ou composée uniquement d'espaces ou de caractères spéciaux"
            ),
            new Assert\Length(
                min: 10,
                max: 250,
                minMessage: 'Votre titre doit comporter au moins {{ limit }} caractères',
                maxMessage: 'Votre titre ne peut pas dépasser {{ limit }} caractères'  
            )
           ]
           
        );
        if (count($violations) > 0) {
            return new JsonResponse(['error' => $violations->get(0)->getMessage()], 400);
        }

        $violations = $validator->validate(
            $nouveauFichier,
            [
                new Assert\NotBlank([
                    'message' => 'Le fichier ne doit pas être vide.',
                ]),
                new Assert\File([
                    'mimeTypes' =>
                    [
                        'application/pdf',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF ou Docx valide.',
                ]),
            ],
            
        );

        if (count($violations) > 0) {
            return new JsonResponse(['error' => $violations->get(0)->getMessage()], 400);
        }

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
            $immersion->setTitre($request->get('titre'));
            $immersion->setLienSupport($request->get('lien_support'));



            $entityManager->flush();
            return new JsonResponse(["message" => "Vous avez édité le cahier de charge avec succès"], 200);
        } else {
            return new JsonResponse(["message" => "Veuillez télecharger un fichier ou le fichier est invalide"], 422);
        }
    }
}
