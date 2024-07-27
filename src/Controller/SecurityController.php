<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\RegistrationType;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Form\LoginFormType;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, UserProviderInterface $userProvider): Response
    {
        $form = $this->createForm(LoginFormType::class);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->addFlash('success', 'Login successful');
            return $this->redirectToRoute('app_homepage');
        }

        // Get login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user
        $lastmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'loginForm' => $form->createView(),
            'last_mail' => $lastmail,
            'error' => $error,
            'login' => true,
        ]);
    }
}
