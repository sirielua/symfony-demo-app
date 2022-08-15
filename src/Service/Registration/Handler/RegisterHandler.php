<?php

namespace App\Service\Registration\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Service\Registration\Command\RegisterCommand;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Registration\Command\SendConfirmationCommand;
use App\Service\Registration\Dto\RegisterDto;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsMessageHandler]
class RegisterHandler
{    
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager,
        private SendConfirmationHandler $sendConfirmationHandler,
        private string $verifyEmailRoute = 'app_verify_email',
        private string $verificationTemplate = 'app/registration/confirmation_email.html.twig',
    ) {}
    
    public function __invoke(RegisterCommand $command): void
    {
        $this->handle($command);
    }
    
    public function handle(RegisterCommand $command): void
    {
        $user = $this->registerUserFromDto($command->getDto());
        
        $sendConfirmationCommand = new SendConfirmationCommand($user->getId());
        $this->sendConfirmationHandler->handle($sendConfirmationCommand);
    }
    
    /**
     * 
     * @param RegisterDto $dto
     * @return UserInterface
     */
    private function registerUserFromDto(RegisterDto $dto): UserInterface
    {
        $user = new User();
        
        $user->setDisplayName($dto->displayName);
        $user->setEmail($dto->email);        
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $dto->plainPassword)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $user;
    }
}
