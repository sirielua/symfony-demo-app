<?php

namespace App\Infrastructure\Type;

enum Environment: string
{
    case DEV = 'dev';
    case PROD = 'prod';
    case TEST = 'test';
    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}