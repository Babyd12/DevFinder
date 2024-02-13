<?php

namespace App\EventSubscriber;

use App\Entity\Apprenant;
use App\Entity\Entreprise;
use App\Entity\Association;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteSubscriber implements EventSubscriberInterface
{
    public function showDeleteMessage(ViewEvent $event): void
    {

        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        // dd($event->getRequest());
        if ((!$entity instanceof Apprenant || !$entity instanceof Association || !$entity instanceof Entreprise)  &&
            in_array($method, [Request::METHOD_DELETE])
        ) {

            $this->runCacheClearCommand();

            $message = 'La ressource a été supprimée avec succès.';
            $response = new JsonResponse(['message' => $message], JsonResponse::HTTP_OK);
            $event->setResponse($response);
        } else {
            return;
        }
    }

    private function runCacheClearCommand(): void
    {
        // Chemin absolu vers le script Symfony
        $symfonyScript = __DIR__ . '/../../bin/console';  // Modifiez le chemin selon votre structure de projet

        // Exécutez la commande de cache clear
        $command = 'php ' . $symfonyScript . ' cache:clear --env=prod';
        exec($command, $output, $returnValue);

        // Affichez la sortie (utile pour le débogage)
        // echo implode("\n", $output);

        // Gérer le cas d'échec
        if ($returnValue !== 0) {
            throw new \RuntimeException('La commande cache:clear a échoué avec le code ' . $returnValue);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['showDeleteMessage', EventPriorities::POST_WRITE],


        ];
    }
}
