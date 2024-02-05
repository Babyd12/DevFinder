<?php

// namespace App\Controller;

// use App\Security\JwtDecoder;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

// class GetUserLoggedController extends AbstractController
// {

//     // ...
//     private $jwtManager;
//     private $tokenStorageInterface;
//     public function __construct(TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
//     {
//         $this->jwtManager = $jwtManager;
//         $this->tokenStorageInterface = $tokenStorageInterface;
//     }

    
//     #[Route('/connexion', name: 'app_get_user_logged', methods: ['POST'] )]
//     public function index()
//     {
//         // $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
//         // return new JsonResponse( ['user' => $decodedJwtToken]);
//     }
// }
