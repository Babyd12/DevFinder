<?php

namespace App\Controller;

use Exception;
use App\Entity\Apprenant;
use App\Entity\Entreprise;
use App\Entity\Association;
use App\Entity\Administrateur;
use App\Services\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustumAdminController extends AbstractController
{
    private $security;
    private $entityManager;
    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/utilisateur/connecte', name: 'app_admin_recuperer_utilisateur_connecter')]
    public function index(): Response
    {
        $entity = $this->security->getUser();
        if ($entity instanceof Apprenant || $entity instanceof Association || $entity instanceof Entreprise  || $entity instanceof Administrateur) {

            $userData = [
                'id' => $entity->getId(),
                'role' => $entity->getRoles(),
                'nom_complet' => $entity->getNomComplet(),
                'email' => $entity->getUserIdentifier(),
                'telephone' => $entity->getTelephone(),
                'description' => $entity->getDescription(),
            ];
            return new JsonResponse($userData, 200);
        }
    }

    #[Route('/api/administrateur/liste/utilisateurs', name: 'app_custum_admin_listeUtilisateur')]
    public function listeEntreprises(): JsonResponse
    {
        $enptreprises = $this->entityManager->getRepository(Entreprise::class)->findAll();
        $association = $this->entityManager->getRepository(Association::class)->findAll();
        $apprenant = $this->entityManager->getRepository(Apprenant::class)->findAll();

        $liste = array_merge($enptreprises, $apprenant, $association);
        return $this->json([
            'utilisateurs' => $liste
        ]);
    }

    #[Route('/api/administrateur/liste/utilisateursBloque', name: 'app_custum_admin_listeUtilisateurBloquer')]
    public function listeUtilisateursBloque(): JsonResponse
    {
        $enptreprises = $this->entityManager->getRepository(Entreprise::class)->findBy(['etat' => true]);
        $association = $this->entityManager->getRepository(Association::class)->findBy(['etat' => true]);
        $apprenant = $this->entityManager->getRepository(Apprenant::class)->findBy(['etat' => true]);

        $liste = array_merge($enptreprises, $apprenant, $association);
        return $this->json([
            'utilisateursBloque' => $liste
        ]);
    }


    #[Route('/api/motDePasseOublie', name: 'app_mot_de_passe_oublie',  methods: ['POST'])]
    public function motDePasseOublier2(Request $request, SendMailService $mailer, UserPasswordHasherInterface  $userPasswordHasherInterface)
    {


        $from = $request->request->all()['email'];
        if (empty($from)) {
            return new JsonResponse(["error" => "Ce champ ne doit pas être vide"]);
        }


        $apprenant = $this->entityManager->getRepository(Apprenant::class)->findOneByEmail($from);
        $association = $this->entityManager->getRepository(Association::class)->findOneByEmail($from);
        $entreprise = $this->entityManager->getRepository(Entreprise::class)->findOneByEmail($from);
        $entity = null;
        if (empty($association) && empty($apprenant) && empty($entreprise)) {
            return new JsonResponse(["error" => "Cette addresse email n'existe pas"]);
        }
        if (!empty($apprenant)) {
            $entity = $apprenant;
        } else if (!empty($association)) {
            $entity = $association;
        } else if (!empty($entreprise)) {
            $entity = $entreprise;
        }

        $roles = ['ROLE_APPRENANT', 'ROLE_ENTREPRISE', 'ROLE_ASSOCIATION'];
        if (!in_array($entity->getRoles()[0], $roles)) {
            return new JsonResponse(['message' => 'Action not autorisé']);
        }

        $plainMotDePasse = $this->generateurDeMotDePasse($entity->getMotDePasse());

        $hashedPassword = $userPasswordHasherInterface->hashPassword($entity, $plainMotDePasse);
        $entity->setMotDePasse($hashedPassword);


        $this->entityManager->beginTransaction();
        try {
            $mailer->sendEmail(
                $mailer->defaultFrom(),
                $from,
                $mailer->getSubjectMotDepasseOublie(),
                $mailer->getTemplateMotDepasseOublie(),
                ['uitilisateur' => $entity, 'nouveauMotDePasse' => $plainMotDePasse]
            );
            // Si l'e-mail est envoyé avec succès, j'enregistre
            $this->entityManager->flush();
            $this->entityManager->commit();
            return new JsonResponse([ 'success' => $mailer->getMssageRetourMotDePasseOublie()]);

        } catch (Exception $e) {
            $this->entityManager->rollback();
            return new JsonResponse(['error' => $e->getMessage()], 200);
        }
    }

    private function generateurDeMotDePasse($characters)
    {
        // Générer un mot de passe aléatoire de longueur
        $length = max(1, intval(strlen($characters) / 2));
        $password = date('m');

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Ajouter les secondes de l'heure actuelle au mot de passe
        $seconds = date('s');
        $password .= $seconds;

        return $password;
    }
}
