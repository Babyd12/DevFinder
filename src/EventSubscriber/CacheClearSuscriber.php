<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class CacheClearSuscriber implements EventSubscriberInterface
{
    public function caheClear(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        if ($entity && in_array($method, [Request::METHOD_POST, Request::METHOD_GET, Request::METHOD_PATCH, Request::METHOD_PUT])) {
            return;
            // $this->runCacheClearCommand();

        } else if (!$entity && in_array($method, [Request::METHOD_DELETE])) {
           return;
            // $this->runCacheClearCommand();

        } else {
            return;
        }
    }

    private function runCacheClearCommand(): void
    {
        // Chemin absolu vers le script Symfony
        $symfonyScript = __DIR__ . '/../../bin/console';  // Modifiez le chemin selon votre structure de projet

        // Exécutez la commande de cache clear
        $command = 'php ' . $symfonyScript . ' cache:clear --env=prod --no-warmup';
        exec($command, $output, $returnValue);

        // Affichez la sortie (utile pour le débogage)
        echo implode("\n", $output);

        // Gérer le cas d'échec
        if ($returnValue !== 0) {
            throw new \RuntimeException('La commande cache:clear a échoué avec le code ' . $returnValue);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['caheClear', EventPriorities::POST_WRITE],
        ];
    }
}
