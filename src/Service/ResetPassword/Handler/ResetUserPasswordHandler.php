<?php

namespace App\Service\ResetPassword\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use App\Service\ResetPassword\SessionHelper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Service\ResetPassword\Command\ResetUserPasswordCommand;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsMessageHandler]
class ResetUserPasswordHandler
{
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private SessionHelper $sessionHelper,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private UserRepository $users,
    ){}
    
    public function __invoke(ResetUserPasswordCommand $command)
    {
        $this->handle($command);
    }
    
    public function handle(ResetUserPasswordCommand $command)
    {
        // A password reset token should be used only once, remove it.
        $this->resetPasswordHelper->removeResetRequest($command->getToken());
            
        $user = $this->loadUser($command->getId());
        $this->updateUserPassword($user, $command->getPassword());
        
        // The session is cleaned up after the password has been changed.
        $this->sessionHelper->cleanSessionAfterReset();
    }
    
    /**
     * 
     * @param type $id
     * @return UserInterface|null
     * @throws \InvalidArgumentException
     */
    private function loadUser($id): ?UserInterface
    {
        if (null === $id) {
            throw new \InvalidArgumentException('User id is not defined');
        }

        $user = $this->users->find($id);

        if (null === $user) {
            throw new \InvalidArgumentException('User not found');
        }
        
        return $user;
    }
    
    private function updateUserPassword(UserInterface $user, string $password)
    {
        // Encode(hash) the plain password, and set it.
        $encodedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setPassword($encodedPassword);
        $this->entityManager->flush();
    }
}
