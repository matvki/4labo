<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\UserType;
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

    #[Route('', name: '')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    // get a specific user by id
    #[Route('/get/{user}', name: '_show')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    // create a new user from a form
    #[Route('/create', name: '_create')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_show', ['user' => $user->getId()]);
        }

        return $this->render('user/createEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // edit an existing user
    #[Route('/edit/{user}', name: '_edit')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('user_show', ['user' => $user->getId()]);
        }

        return $this->render('user/createEdit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    // delete an existing user
    #[Route('/delete/{user}', name: '_delete')]
    public function delete(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('user');
    }

    #[Route('/success', name: '_success')]
    public function success(): Response
    {
        return $this->render('user/success.html.twig');
    }
}