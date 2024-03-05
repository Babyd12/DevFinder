<?php

namespace App\State;

use App\Entity\Projet;
use App\Entity\Apprenant;
use App\Entity\ProjetStatu;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Action\NotFoundAction;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Url;

class ProjetStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processorInterface,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private RequestStack $requestStack,
        private ValidatorInterface $validator,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof Projet && $operation instanceof Post) {

            $request = $this->requestStack->getCurrentRequest()->request;
            $role = $context['operation']->getSecurity();

            if ($uriVariables['id'] === 0) {
                $data->setStatu(ProjetStatu::en_cours);
                $data->setAssociation($this->security->getUser());
                if (empty($data->getCahierDecharge())) {
                    return new JsonResponse(["error" => "Veuilelz fournir un cachier de charge"], 417);
                }
                $existProjetName = $this->entityManager->getRepository(Projet::class)->findOneBy(['titre' => $data->getTitre()]);
                if (!empty($existProjetName)) {
                    return new JsonResponse(["error" => "Un projet prote déjà ce nom"], 400);
                }

                return $this->processorInterface->process($data, $operation, $uriVariables, $context);
            }


            if (empty($uriVariables['id'])) {
                return new JsonResponse(["error" => "Veuillez fournir le paramètre"]);
            }

            $existProjet = $this->entityManager->getRepository(Projet::class)->find($uriVariables['id']);
            if ($existProjet  == null) {
                return new JsonResponse(["error" => "Ressource non trouvé"], 404);
            }

            switch ($role) {
                case strpos($role, 'ROLE_ASSOCIATION') !== false:
                    if ($data->getTitre()) {
                    }
                    $nouveauNomProjet = $this->entityManager->getRepository(Projet::class)->findOneBy(['id' => $uriVariables['id'], 'titre' => $existProjet->getTitre()]);
                    // dd($nouveauNomProjet, $data);
                    if (empty($nouveauNomProjet)) {
                        $existProjetName = $this->entityManager->getRepository(Projet::class)->findOneBy(['titre' => $existProjet->getTitre()]);
                        if (!empty($existProjetName)) {

                            return new JsonResponse(["error" => "Un projet prote déjà ce nom"], 400);
                        }
                    }
                    break;

                case strpos($role, 'ROLE_APPRENANT') !== false:
                    $lien = $request->get('lien_du_repertoire_distant');
                    if (empty($lien)) {
                        return new JsonResponse(["error" => "Veuillez fournir une url"], 400);
                        $erreurs = $this->validator->validate($lien, new Url());
                        // Au caus ou l'exception n'est pas lévé depuis la Validator\Constraints\Url
                        if (count($erreurs) > 0) {
                            return new JsonResponse(["message" => "L'URL n'est pas valide",], 422);
                        }
                    }
                    break;
            }
            return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        } else {
            return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        }
    }

}
