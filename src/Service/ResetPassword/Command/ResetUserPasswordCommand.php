<?php

namespace App\Service\ResetPassword\Command;

class ResetUserPasswordCommand
{
    private int $id;
    private string $password;
    private string $token;
    
    public function __construct(int $id, string $password, string $token)
    {
        $this->id = $id;
        $this->password = $password;
        $this->token = $token;
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function getToken(): string
    {
        return $this->token;
    }
}
