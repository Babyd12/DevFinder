<?php

namespace App\DataFixtures;

use App\Entity\Apprenant;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
;

class ApprenantFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public function __construct( UserPasswordHasherInterface $hasher)
    {
        $this -> hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $email = 'apprenant@apprenant.com';
        $apprenant = new Apprenant();
        $apprenant->setNomComplet('apprenant');
        $apprenant->setEmail($email);
        $password = $this->hasher->hashPassword($apprenant, 'Animaleman24@');
        $apprenant->setMotDePasse($password);
        $apprenant->setTelephone('784443232');
        $apprenant->setDescription('This is the new description  for this apprenant user and will be used   to authenticate');
        $apprenant->setRoles($apprenant->getRoles());
        $exist  = $manager->getRepository(Apprenant::class)->findOneBy(['email' => $email]);
        if(!$exist){
            $manager->persist($apprenant);     
        }
        $manager->flush();
    }
}
