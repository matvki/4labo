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

    // get a specific user by id
    #[Route('/show/{user}', name: '_profile')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }


}