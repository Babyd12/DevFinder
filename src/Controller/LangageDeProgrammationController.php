<?php

namespace App\Controller;

use App\Entity\LangageDeProgrammation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class LangageDeProgrammationController extends AbstractController
{

    private Security $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
        
    }
    
    #[Route('/langage/update/rien{id}',) ]
    public function edit(EntityManagerInterface $entityManager, $id ): JsonResponse
    {
      
        $langage = $entityManager->getRepository(LangageDeProgrammation::class)->find($id);
        if (!$langage ) {
            return new JsonResponse(["message" => " Ce langage de programmation n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        if($langage instanceof LangageDeProgrammation &&  $langage->isUsedInProjects()  )
        {
            return new JsonResponse(["message" => "Ce langage est utilé dans des projets impossible de l'éditer"], Response::HTTP_UNAUTHORIZED);

        }
        $langage->flush();
        $showData = [
            'Langage' => $langage->nom,
        

        ];
        return new JsonResponse(['message' => 'Vous avez été édité ce projet avec succès', 'données' => $showData], Response::HTTP_OK);
    }
}
