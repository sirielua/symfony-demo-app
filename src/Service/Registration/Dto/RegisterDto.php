<?php

namespace App\Service\Registration\Dto;

class RegisterDto
{
    public function __construct(
        public readonly string $displayName,
        public readonly string $email,
        public readonly string $plainPassword
    ) {}
}
