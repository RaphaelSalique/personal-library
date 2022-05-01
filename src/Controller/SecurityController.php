<?php

namespace App\Controller;

use App\Exception\LogoutNotActivatedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): Response
    {
        throw new LogoutNotActivatedException('Don\'t forget to activate logout in security.yaml');
    }
}
