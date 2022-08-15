<?php

namespace App\Service\Registration\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Repository\UserRepository;
use App\Service\Registration\EmailVerifier;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\Registration\Command\SendConfirmationCommand;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Infrastructure\Type\Environment;
use App\Infrastructure\Type\Flash;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class SendConfirmationHandler
{    
    public function __construct(
        private UserRepository $users,
        private EmailVerifier $emailVerifier,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
        private string $verifyEmailRoute = 'app_verify_email',
        private string $verificationTemplate = 'app/registration/confirmation_email.html.twig',
    ) {}
    
    public function __invoke(SendConfirmationCommand $command): void
    {
        $this->handle($command);
    }
    
    public function handle(SendConfirmationCommand $command): void
    {
        $user = $this->loadUser($command->getId());
        $this->sendEmailConfirmation($user);
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
     * Generate a signed URL and email it to the user
     * 
     * @param UserInterface $user
     * @return void
     */
    private function sendEmailConfirmation(UserInterface $user): void
    {
        if ($this->parameterBag->get('kernel.environment') === (Environment::DEV)->value) {
            if (php_sapi_name() !== 'cli') {
                $signedUrl = $this->emailVerifier->getSignedUrl($this->verifyEmailRoute, $user);
                $this->requestStack
                    ->getSession()
                    ->getFlashBag()
                    ->add((Flash::SUCCESS)->value, 'Your confirmation link: <a href="'.$signedUrl.'">'.$signedUrl.'</a>');
            }
        } else {
            $this->emailVerifier->sendEmailConfirmation(
                verifyEmailRouteName: $this->verifyEmailRoute,
                user: $user,
                email: (new TemplatedEmail())
                    ->from(new Address($this->parameterBag->get('mailer_sender_email'), $this->parameterBag->get('mailer_sender_name')))
                    ->to($user->getEmail())
                    ->subject($this->parameterBag->get('mailer_confirm_email_subject'))
                    ->htmlTemplate($this->verificationTemplate)
            );
        }
    }
}
