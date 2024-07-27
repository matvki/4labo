<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last email entered by the user
        $lastmail = $authenticationUtils->getLastUsername();

        // Display POST data
        if ($request->isMethod('POST')) {
            dd();
            $postData = $request->request->all();
            dump($postData); // Dump POST data for debugging
            die(); // Stop execution to view the dump
        }

        return $this->render('security/login.html.twig', [
            'last_mail' => $lastmail,
            'error' => $error,
            'login' => true
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \Exception('Bye !');
    }
}

