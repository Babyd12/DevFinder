<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;




class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpExceptionInterface) {
            switch (true) {
                case $exception instanceof AccessDeniedException || $exception instanceof AccessDeniedHttpException:
                    $statusCode = JsonResponse::HTTP_FORBIDDEN;
                    $errorMessage = sprintf('Action non autorisée: code %s', $statusCode );
                    break;
        
                // Ajoutez d'autres cas pour d'autres types d'exceptions HTTP si nécessaire
                case $exception instanceof NotFoundHttpException:
                    $errorMessage = sprintf('Ressource non trouvée: %s', $exception->getcode());
                    $statusCode = JsonResponse::HTTP_NOT_FOUND;
                    break;
                
                case $exception instanceof \Symfony\Component\Security\Core\Exception\AuthenticationException &&
                    strpos($exception->getMessage(), 'You cannot refresh a user from the EntityUserProvider') !== false:
                    $errorMessage = 'Erreur de rafraîchissement de l\'utilisateur sans identifiant.';
                    $errorMessage = sprintf('%s: code %s',$exception->getMessage(), $exception->getCode());
                    break;

                // case $exception instanceof \Doctrine\ORM\Exception\ORMException && $this->isDeleteSuccessful($exception):
                //     $errorMessage = 'Suppression réussie.';
                //     $response = new JsonResponse(['message' => $errorMessage], JsonResponse::HTTP_OK);
                //     $event->setResponse($response);
                //     $statusCode = JsonResponse::HTTP_OK;
                //     break;

                default:
                $statusCode = JsonResponse::HTTP_EXPECTATION_FAILED;
                    $errorMessage = sprintf('Erreur HTTP: %s', $statusCode);
                    break;
            } 
        
            $response = new JsonResponse(['error' => $errorMessage], $statusCode);
            $event->setResponse($response);
        } 
    
    
    }
}