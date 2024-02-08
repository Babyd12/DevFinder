<?php

namespace App\DataFixtures;

use App\Entity\Administrateur;
use App\Repository\AdministrateurRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

;

class AdminFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public function __construct( UserPasswordHasherInterface $hasher)
    {
        $this -> hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $email = 'admin@admin.com';
        $admin = new Administrateur();
        $admin->setNomComplet('admin');
        $admin->setEmail($email);
        $password = $this->hasher->hashPassword($admin, 'Animaleman24@');
        $admin->setPassword($password);
        $admin->setRoles($admin->getRoles());
        $exist  = $manager->getRepository(Administrateur::class)->findOneBy(['email' => $email]);
        if(!$exist){
            $manager->persist($admin);     
        }
        $manager->flush();
    }
}
