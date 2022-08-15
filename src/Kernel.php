<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use App\Infrastructure\Type\Environment;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    
    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
        
        $allowedEnvironments = Environment::values();
        if (!in_array($environment, $allowedEnvironments)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid environment provided to "%s": the environment should be one of %s', 
                get_debug_type($this), 
                implode(', ', $allowedEnvironments), 
            ));
        }
    }
}
