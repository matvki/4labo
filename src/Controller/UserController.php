<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\EditFormType;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    private Request $request;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager    = $entityManager;
        $this->passwordHasher   = $passwordHasher;
    }

    // get a specific user by id
    #[Route('/show/{user}', name: '_profile')]
    public function show(User $user): Response
    {
        // check if user and current user are the same
        if ($user->getId() !== $this->getUser()->getId())
            return $this->redirectToRoute('home');
// dd($user->getProducts()[0]);
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

        $form = $this->createForm(EditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_profile', ['user' => $user->getId()]);
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit Profile',
        ]);
    }

    #[Route('/edit_password/{user}', name: '_edit_password')]
    public function changePassword(Request $request, User $user): Response
    {
        // check if user and current user are the same
        if ($user->getId() !== $this->getUser()->getId())
            return $this->redirectToRoute('home');

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword    = $form->get('plainPassword')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_profile', ['user' => $user->getId()]);
        }

        return $this->render('user/editPassword.html.twig', [
            'form' => $form->createView(),
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