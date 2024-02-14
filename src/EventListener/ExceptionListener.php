<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            switch (true) {

                case ($exception instanceof AuthenticationException && strpos($exception->getMessage(), 'Expired JWT Token') !== false):
                    $statusCode = JsonResponse::HTTP_UNAUTHORIZED;
                    $errorMessage = sprintf('Token JWT expiré. Veuillez vous reconnecter. %d', $statusCode);
                    break;                

                case $exception instanceof AccessDeniedException || $exception instanceof AccessDeniedHttpException:
                    $statusCode = JsonResponse::HTTP_FORBIDDEN;
                    $errorMessage = sprintf('Action non autorisée: code %s', $statusCode);
                    break;

                case $exception instanceof NotFoundHttpException:
                    $statusCode = JsonResponse::HTTP_NOT_FOUND;
                    $errorMessage = sprintf('Ressource non trouvée: %s', $exception->getcode());
                    break;

                case $exception instanceof \Symfony\Component\Security\Core\Exception\AuthenticationException &&
                    strpos($exception->getMessage(), 'You cannot refresh a user from the EntityUserProvider') !== false:
                    $errorMessage = 'Erreur de rafraîchissement de l\'utilisateur sans identifiant.';
                    $errorMessage = sprintf('%s: code %s', $errorMessage, $exception->getCode());
                    break;

                case  $exception instanceof InvalidArgumentException:
                    $statusCode = JsonResponse::HTTP_BAD_REQUEST;
                    $errorMessage = 'Token invalide ou utilisateur innexistant.';
                    $response = new JsonResponse(['error' => 'Token invalide ou utilisateur innexistant.'], 400);
                    break;

                case $exception->getCode() == 401:
                    $errorMessage = 'Information de connexion invalide';
                    $response = sprintf('Erreur : %s', $errorMessage);
                    
                    break;

                case $exception->getCode() == 417:
                    $statusCode = $exception->getCode();
                    $errorMessage = 'Erreur de verbe, vous aviez utilisé Put à ';
                    $response = sprintf('Erreur : %s', $errorMessage);
                    break;

                case $exception->getStatusCode() === 400:
                    $statusCode = JsonResponse::HTTP_BAD_REQUEST;
                    $errorMessage = sprintf('Erreur de décodage JSON. Vérifiez la validité de la chaîne JSON.: %d original message: %s', $statusCode, $exception->getMessage());
                    // $response = sprintf('Erreur : %s', $errorMessage);
                    break;

                default:
                    $statusCode = JsonResponse::HTTP_EXPECTATION_FAILED;
                    $errorMessage = sprintf('Erreur HTTP: %s, Detail: %s', $statusCode, $exception->getMessage());
                    // $response = sprintf('Erreur : %s', $errorMessage);
                    break;
            }

            $response = new JsonResponse(['error' => $errorMessage], $statusCode);
            $event->setResponse($response);
            // $event->stopPropagation();
        }
    }
}
