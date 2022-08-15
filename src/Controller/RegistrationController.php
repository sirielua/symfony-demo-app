<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Registration\Exception\VerificationFailedException;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Infrastructure\Type\Flash;
use App\Service\Registration\Dto\RegisterDto;
use App\Service\Registration\Command\RegisterCommand;
use App\Service\Registration\Command\VerifyCommand;

#[Route('/register')]
class RegistrationController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $commandBus,     
    ) {}
    
    #[Route('', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $dto = new RegisterDto(
                $form->get('displayName')->getData(),
                $form->get('email')->getData(),
                $form->get('plainPassword')->getData()
            );
            
            $this->commandBus->dispatch(new RegisterCommand($dto));
            
            $this->addFlash((Flash::INFO)->value, 'We have sent you a confirmation message. Please check your email.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify-email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        try {
            $this->commandBus->dispatch(new VerifyCommand($request->get('id'), $request->getUri()));
        } catch (VerificationFailedException $exception) {
            $this->addFlash((Flash::ERROR)->value, $exception->getMessage());
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash((Flash::SUCCESS)->value, 'Your email address has been verified.');
        return $this->redirectToRoute('app_login');
    }
}
