<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

class HomeController extends AbstractController
{
    private EntityManagerInterface $emi;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->emi = $entityManager;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // get 10 last posts
        $posts = $this->emi->getRepository(Product::class)->findBy([], ['id' => 'DESC'], 10);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts
        ]);
    }
}
