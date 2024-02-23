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
        // if ($openApiparam != null) {

        //     $method = $openApiparam->getMethod();
        //     $entity = $request->attributes->get('data');
        //     $contentType = $request->headers->get('Content-Type');
        //     $controllerClassName = $openApiparam->getClass();

        //     // dd($openApiparam);
        //     $searchMultipart = "multipart/form-data";

        //     // dd($request->attributes, $openApiparam->getClass());

        //     if ($controllerClassName == "App\Entity\Projet" && $method == "POST" &&  strpos($contentType, 'multipart/form-data') !== false) {
        //         $this->ConvertToInteger($request, 'nombre_de_participant');
        //         // dd(var_dump($request->getContentType()));
        //     } else {
        //         die('not implemented if');
        //     }
        // } else {
        // }
    }


    /**
     * @param Request
     * @info convert one field to integer
     */
    public function ConvertToInteger($request, $field)
    {
        //je recupere le contenu sous format tableau
        $data = json_decode($request->getContentType(), true);
        dd($data);
        // je vérifie si le champ "nombre_de_participant" est défini dans les données
        if (isset($data[$field])) {
            $data[$field] = (int)$data[$field];
            dd(var_dump($data[$field]));
            // je met à jour le contenu de la requête
            $request->setContent(json_encode($data));
        } else {
            dd('die');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',

        ];
    }
}
