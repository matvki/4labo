<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

#[AsCommand(
    name: 'app:check-password',
    description: 'Check if the given plain password matches the hashed password for the specified user.',
)]
class CheckPasswordCommand extends Command
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('mail', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('plainPassword', InputArgument::REQUIRED, 'The plain password to check.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $mail = $input->getArgument('mail');
        $plainPassword = $input->getArgument('plainPassword');

        // Fetch the user based on the provided email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['mail' => $mail]);

        if (!$user) {
            $io->error('User not found.');
            return Command::FAILURE;
        }

        // Check if the provided plain password matches the hashed password
        $isPasswordValid = $this->passwordHasher->isPasswordValid($user, $plainPassword);

        if ($isPasswordValid) {
            $io->success('Password is valid.');
            return Command::SUCCESS;
        } else {
            $io->error('Invalid password.');
            return Command::FAILURE;
        }
    }
}
