<?php

namespace App\Command;

use App\Entity\Administrateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




#[AsCommand(
    name: 'app:CreateAdmin',
    description: 'Add a short description for your command',
)]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager,  private UserPasswordHasherInterface $userPasswordHasherInterface,)
    {
        parent::__construct('app:CreateAdmin');

        $this->entityManager = $entityManager;
    }



    protected function configure(): void
    {
        $this
            ->addArgument('nom_complet', InputArgument::OPTIONAL, 'Administrateur')
            ->addArgument('email', InputArgument::OPTIONAL, 'admin@admin.com')
            ->addArgument('mot_de_passe', InputArgument::OPTIONAL, 'Animaleman24@')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);
        $nom_complet = $input->getArgument('nom_complet');
        if (!$nom_complet) {
            $question = new Question('Quel est le nom de l\'administrateur : ');
            $nom_complet = $helper->ask($input, $output, $question);
        }

        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('Quel est l\'email de ' . $nom_complet . ' : ');
            $email = $helper->ask($input, $output, $question);
        }
         
        $plainPassword = $input->getArgument('mot_de_passe');
        if (!$plainPassword) {
            $question = new Question('Quel est le mot de passe de ' . $nom_complet . ' : ');

            $plainPassword = $helper->ask($input, $output, $question);
        }

        // $hashedPassword = $this->userPasswordHasherInterface->hashPassword($entity, $plainPassword);
        // $entity->setMotDePasse($hashedPassword);
        $existEntity = $this->entityManager->getRepository(Administrateur::class)->findAll();
        if ($existEntity !== null) {
            // Créer une requête DQL pour supprimer toutes les lignes de la table
            $query = $this->entityManager->createQuery('DELETE FROM App\Entity\Administrateur');
            // Exécuter la requête de suppression
            $query->execute();
        }
        $entity = new Administrateur();
        $entity->setNomComplet($nom_complet);
        $entity->setEmail($email);
        $entity->setEtat(false);
        $entity->setPassword($this->userPasswordHasherInterface->hashPassword($entity, $plainPassword));

        // $user = (new Administrateur())->setNomComplet($nom_complet)
        //     ->setEmail($email)
        //     ->setMotDePasse($hashedPassword)
        //     ->setRoles(['ROLE_ADMIN'])
        //     ->setEtat(false);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $io->success('Le nouvel administrateur a été créé !');

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
