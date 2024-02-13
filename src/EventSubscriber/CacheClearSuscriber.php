<?php

namespace App\EventSubscriber;

use Symfony\Component\Process\Process;
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
            
            $this->runCacheClearCommand();

        } else if (!$entity && in_array($method, [Request::METHOD_DELETE])) {
           
            $this->runCacheClearCommand();

        } else {
            return;
        }
    }

    private function runCacheClearCommand(): void
    {
         // Exécuter la commande de cache clear
         $command = 'php bin/console cache:clear --env=prod';
         $process = new Process(explode(' ', $command));
         $process->run();
 
         if (!$process->isSuccessful()) {
             // Gérer les erreurs si nécessaire
             throw new \RuntimeException('La commande cache:clear a échoué.');
         }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['caheClear', EventPriorities::POST_WRITE],
        ];
    }
}
