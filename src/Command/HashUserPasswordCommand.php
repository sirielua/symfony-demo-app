<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;

#[AsCommand(
    name: 'app:hash-user-password', 
    description: 'Create hash using user password hasher',
)]
class HashUserPasswordCommand extends Command
{    
    private UserPasswordHasherInterface $paswordHasher;
    
    public function __construct(UserPasswordHasherInterface $paswordHasher)
    {
        $this->paswordHasher = $paswordHasher;
        
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::OPTIONAL, 'User identifier (i.e username or email, depends on provider)')
            ->addArgument('password', InputArgument::OPTIONAL, 'Plain password')
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command creates hash and outputs it to console')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $this->getIdentifier($input, $output);
        if (empty($identifier)) {
            $output->writeln('User identifier is required');
            return Command::INVALID;
        }
        
        $password = $this->getPassword($input, $output);
        if (empty($password)) {
            $output->writeln('Password is required');
            return Command::INVALID;
        }
        
        $passwordHash = $this->generatePasswordHash($identifier, $password);
        if (empty($passwordHash)) {
            $output->writeln('Password hashing failed');
            return Command::FAILURE;
        }
        
        $output->writeln(['Password succesfully hashed: ', $passwordHash]);
        
        return Command::SUCCESS;
    }
    
    private function getIdentifier(InputInterface $input, OutputInterface $output): ?string
    {
        $identifier = $input->getArgument('identifier');
        if (null === $identifier) {
            $helper = $this->getHelper('question');
            
            $question = new Question(
                'Please input ' 
                . lcfirst($this->getDefinition()->getArgument('identifier')->getDescription()) 
                . ': ' . PHP_EOL,
            );
            
            $identifier = $helper->ask($input, $output, $question);
        }
        
        return $identifier;
    }
    
    private function getPassword(InputInterface $input, OutputInterface $output): ?string
    {
        $password = $input->getArgument('password');
        if (null === $password) {
            $helper = $this->getHelper('question');
            
            $question = new Question(
                'Please input ' 
                . lcfirst($this->getDefinition()->getArgument('password')->getDescription()) 
                . ': ' . PHP_EOL,
            );
            $question->setHidden(true);
            $question->setHiddenFallback(false);
    
            $password = $helper->ask($input, $output, $question);
        }
        return $password;
    }
    
    private function generatePasswordHash(string $identifier, string $password): ?string
    {
        $user = new InMemoryUser($identifier, null);
        return $this->paswordHasher->hashPassword($user, $password);
    }
}
