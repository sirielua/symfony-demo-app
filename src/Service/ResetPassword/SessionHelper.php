<?php

namespace App\Service\ResetPassword;

use Symfony\Component\HttpFoundation\RequestStack;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionHelper
{
    public function __construct(
        private RequestStack $requestStack,
    ){}
    
    public function storeTokenInSession(string $token): void
    {
        $this->getSessionService()->set('ResetPasswordPublicToken', $token);
    }

    public function getTokenFromSession(): ?string
    {
        return $this->getSessionService()->get('ResetPasswordPublicToken');
    }

    public function setTokenObjectInSession(ResetPasswordToken $token): void
    {
        $token->clearToken();

        $this->getSessionService()->set('ResetPasswordToken', $token);
    }

    public function getTokenObjectFromSession(): ?ResetPasswordToken
    {
        return $this->getSessionService()->get('ResetPasswordToken');
    }

    public function cleanSessionAfterReset(): void
    {
        $session = $this->getSessionService();

        $session->remove('ResetPasswordPublicToken');
        $session->remove('ResetPasswordCheckEmail');
        $session->remove('ResetPasswordToken');
    }

    private function getSessionService(): SessionInterface
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request->getSession();
    }
}
