<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{   
    #[Route('/', name: 'app_index')]
    public function actionIndex()
    {
        return $this->render('index/index.html.twig');
    }
}
