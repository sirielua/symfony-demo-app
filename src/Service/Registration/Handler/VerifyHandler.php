<?php

namespace App\Service\Registration\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Repository\UserRepository;
use App\Service\Registration\EmailVerifier;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Registration\Command\VerifyCommand;
use App\Service\Registration\Exception\VerificationFailedException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsMessageHandler]
class VerifyHandler
{
    public function __construct(
        private UserRepository $users,
        private EmailVerifier $emailVerifier,
        private TranslatorInterface $translator,
    ) {}
    
    public function __invoke(VerifyCommand $command): void
    {
        $this->handle($command);
    }
    
    public function handle(VerifyCommand $command): void
    {        
        $user = $this->loadUser($command->getId());
        $this->verifyUserEmail($user, $command->getRequestUri());
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
    
    /**
     * Validate email confirmation link, sets User::isVerified=true and persists
     * 
     * @param UserInterface $user
     * @param string|null $requestUri
     * @return void
     * @throws VerificationFailedException
     */
    private function verifyUserEmail(UserInterface $user, ?string $requestUri = null): void
    {
        try {
            $this->emailVerifier->handleEmailConfirmation($requestUri, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $message = $this->translator->trans($exception->getReason(), [], 'VerifyEmailBundle');
            throw new VerificationFailedException($message);
        }
    }
}
