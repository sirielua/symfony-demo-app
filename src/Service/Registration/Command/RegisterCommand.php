<?php

namespace App\Service\Registration\Command;

use App\Service\Registration\Dto\RegisterDto;

class RegisterCommand
{
    private RegisterDto $dto;
    
    public function __construct(RegisterDto $dto)
    {
        $this->dto = $dto;
    }
    
    public function getDto(): RegisterDto
    {
        return $this->dto;
    }
}
