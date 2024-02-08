<?php

namespace App\DataFixtures;

use App\Entity\Entreprise;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
;

class EntreprisetFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;
    public function __construct( UserPasswordHasherInterface $hasher)
    {
        $this -> hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $email = 'entreprise@entreprise.com';
        $entreprise = new Entreprise();
        $entreprise->setNomComplet('entreprise');
        $entreprise->setEmail($email);
        $password = $this->hasher->hashPassword($entreprise, 'Animaleman24@');
        $entreprise->setMotDePasse($password);
        $entreprise->setTelephone('784443232');
        $entreprise->setDescription('This is the new description  for this entreprise user and will be used   to authenticate');
        $entreprise->setNumeroIdentificationNaitonal('3243805 0LM');
        $entreprise->setRoles($entreprise->getRoles());
        $exist  = $manager->getRepository(Entreprise::class)->findOneBy(['email' => $email]);
        if(!$exist){
            $manager->persist($entreprise);     
        }
        $manager->flush();
    }
}
