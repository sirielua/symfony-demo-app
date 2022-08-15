<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class TestController extends AbstractController
{    
    #[Route('/test', name: 'app_test')]
    public function actionIndex()
    {
        return new Response();
    }
}
