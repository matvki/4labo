<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    private Request $request;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager    = $entityManager;
    }

    // get a specific user by id
    #[Route('/show/{user}', name: '_profile')]
    public function show(User $user): Response
    {
        // check if user and current user are the same
        if ($user->getId() !== $this->getUser()->getId())
            return $this->redirectToRoute('home');

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    // edit a specific user by id
    #[Route('/edit/{user}', name: '_edit')]
    public function edit(User $user, Request $request): Response
    {
        // check if user and current user are the same
        if ($user->getId() !== $this->getUser()->getId())
            return $this->redirectToRoute('home');

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('user_show', ['user' => $user->getId()]);
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit Profile',
        ]);
    }

    // delete a specific user by id
    #[Route('/delete/{user}', name: '_delete')]
    public function delete(User $user): Response
    {
        // check if user and current user are the same
        if ($user->getId() !== $this->getUser()->getId())
            return $this->redirectToRoute('home');

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('home');
    }
}