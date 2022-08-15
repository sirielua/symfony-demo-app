<?php

namespace App\Service\Registration\Command;

class VerifyCommand
{
    private int $id;
    private ?string $requestUri;
    
    public function __construct(int $id, ?string $requestUri)
    {
        $this->id = $id;
        $this->requestUri = $requestUri;
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getRequestUri(): ?string
    {
        return $this->requestUri;
    }
}
