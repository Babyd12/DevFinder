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
        // dd($exception);
        if ($exception instanceof HttpExceptionInterface) {
            switch (true) {
                case $exception instanceof AccessDeniedException || $exception instanceof AccessDeniedHttpException:
                    $errorMessage = sprintf('Action non autorisée: code %s', $exception->getCode());
                    $statusCode = JsonResponse::HTTP_FORBIDDEN;
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

                default:
                    $errorMessage = sprintf('Erreur HTTP: %s', $exception->getCode());
                    $statusCode = JsonResponse::HTTP_EXPECTATION_FAILED;
                    break;
            }
        
            $response = new JsonResponse(['error' => $errorMessage], $statusCode);
            $event->setResponse($response);
        } 
    
    
    }
}