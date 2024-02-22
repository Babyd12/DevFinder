<?php

namespace App\EventSubscriber;

use App\Entity\Projet;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BeforeValidationSubscriber implements EventSubscriberInterface
{
    // public function __construct(private ViewEvent $viewEvent){}

    public function onKernelRequest(RequestEvent $event): void
    {
        // $request = $event->getRequest();

        // $openApiparam  = $request->attributes->get('_api_operation');
        // $method = $openApiparam->getMethod();
        // $entity = $request->attributes->get('data');
        // $contentType =$request->headers->get('Content-Type');
        
       
        // if($entity instanceof Projet && $method == "POST" && $contentType == "multipart/form-data"){
        //    $this->ConvertToInteger($request, 'nombre_de_participant');
        // }

    }


    /**
     * @param Request
     * @info convert one field to integer
     */
    public function ConvertToInteger($request, $field)
    {
        if ($request->isMethod('POST') && $request->getContentType() == 'multipart/form-data') {

            //je recupere le contenu sous format tableau
            $data = json_decode($request->getContent(), true);

            // je vérifie si le champ "nombre_de_participant" est défini dans les données
            if (isset($data[$field])) {
                $data[$field] = (int)$data[$field];

                // je met à jour le contenu de la requête
                $request->setContent(json_encode($data));
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            
        ];
    }
}
