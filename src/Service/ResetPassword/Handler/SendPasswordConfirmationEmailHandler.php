<?php

namespace App\Service\ResetPassword\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\ResetPassword\SessionHelper;

use App\Service\ResetPassword\Command\SendPasswordConfirmationEmailCommand;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

use App\Infrastructure\Type\Environment;
use App\Infrastructure\Type\Flash;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class SendPasswordConfirmationEmailHandler
{
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $router,
        private MailerInterface $mailer,
        private TranslatorInterface $translator,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
        private SessionHelper $sessionHelper,
        private string $resetPasswordRoute = 'app_reset_password',
        private string $resetPasswordEmailTemplate = 'app/reset_password/email.html.twig',
    ){}
    
    public function __invoke(SendPasswordConfirmationEmailCommand $command): void
    {
        $this->handle($command);
    }
    
    public function handle(SendPasswordConfirmationEmailCommand $command): void
    {
        $user = $this->loadUserByEmail($command->getEmail());
        
        // Do not reveal whether a user account was found or not.
        if ($user) {
            $this->processSendingPasswordResetEmail($user, $command->getEmail());
        }
    }
    
    private function loadUserByEmail(string $email): ?UserInterface
    {
        if (!$email) {
            return null;
        }
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        if (!$user) {
            return null;
        }

        return $user;
    }
    
    /**
     * 
     * @param UserInterface $user
     * @param string $email
     * @return void
     */
    private function processSendingPasswordResetEmail(UserInterface $user, string $email): void
    {        
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);

            if ($this->parameterBag->get('kernel.environment') === (Environment::DEV)->value) {
                $resetUrl = $this->router->generate($this->resetPasswordRoute, ['token' => $resetToken->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
                
                $this->requestStack
                    ->getSession()
                    ->getFlashBag()
                    ->add((Flash::SUCCESS)->value, 'Your reset password link: <a href="'.$resetUrl.'">'.$resetUrl.'</a>');
            } else {
                $this->sendResetPasswordEmail($resetToken, $user, $email);
            }
            
            // Store the token object in session for retrieval in check-email route.
            $this->sessionHelper->setTokenObjectInSession($resetToken);
        } catch (ResetPasswordExceptionInterface $e) {
            if ($this->parameterBag->get('kernel.environment') === (Environment::DEV)->value) {
                $this->requestStack
                    ->getSession()
                    ->getFlashBag()
                    ->add((Flash::ERROR)->value, sprintf(
                        '%s - %s',
                        $this->translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
                        $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle')
                    ));
            }
        }
    }

    /**
     * 
     * @param ResetPasswordToken $resetToken
     */
    private function sendResetPasswordEmail(ResetPasswordToken $resetToken, string $email)
    {
        $envelope = (new TemplatedEmail())
            ->from(new Address($this->parameterBag->get('mailer_sender_email'), $this->parameterBag->get('mailer_sender_name')))
            ->to($email)
            ->subject($this->parameterBag->get('mailer_reset_password_subject'))
            ->htmlTemplate($this->resetPasswordEmailTemplate)
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $this->mailer->send($envelope);
    }
}
