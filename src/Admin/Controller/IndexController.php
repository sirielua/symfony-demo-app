<?php

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class IndexController extends AbstractController
{   
    #[Route('/admin', name: 'admin_index')]
    public function index(): Response
    {
        return $this->render('@admin/index/index.html.twig');
    }
    
    #[Route('/admin/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('@admin/index/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    
    #[Route('/admin/logout', name: 'admin_logout', methods: ['GET','POST'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
    
    #[Route('/admin/phpinfo', name: 'admin_phpinfo')]
    public function info(): Response
    {
        ob_start();
        phpinfo();
        $output = ob_get_contents();
        ob_get_clean();
        
        return new Response($output);
    }
    
    
//    #[Route('/hash', name: 'app_hash')]
//    public function hash(Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher)
//    {
//        // ... e.g. get the user data from a registration form
//        $user = new \Symfony\Component\Security\Core\User\InMemoryUser('siriel@ukr.net', null);
//        $plaintextPassword = 'gfhjdjp';
//
//        // hash the password (based on the security.yaml config for the $user class)
//        $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
//        
//        echo $hashedPassword;
//        exit;
//    }
}
