<?php

namespace App\DataFixtures;

use App\Entity\Association;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
;

class AssociationFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public function __construct( UserPasswordHasherInterface $hasher)
    {
        $this -> hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $email = 'association@association.com';
        $association = new Association();
        $association->setNomComplet('association');
        $association->setEmail($email);
        $password = $this->hasher->hashPassword($association, 'Animaleman24@');
        $association->setMotDePasse($password);
        $association->setTelephone('784443232');
        $association->setDescription('This is the new description  for this association user and will be used   to authenticate');
        $association->setNumeroIdentificationNaitonal('3243805 0LM');
        $association->setRoles($association->getRoles());
        $exist  = $manager->getRepository(Association::class)->findOneBy(['email' => $email]);
        if(!$exist){
            $manager->persist($association);     
        }
        $manager->flush();
    }
}
