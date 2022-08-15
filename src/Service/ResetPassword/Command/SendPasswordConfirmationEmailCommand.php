<?php

namespace App\Service\ResetPassword\Command;

class SendPasswordConfirmationEmailCommand
{
    private string $email;
    
    public function __construct(string $email)
    {
        $this->email = $email;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
}
