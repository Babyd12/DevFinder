<?php
namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JwtDecoder
{
    // private $tokenStorage;

    // public function __construct(TokenStorageInterface $tokenStorage)
    // {
    //     $this->tokenStorage = $tokenStorage;
    // }

    // public function decodeToken(string $token): array
    // {
    //     return $this->jwtManager->decode($token);
    // }
}

