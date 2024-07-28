<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use App\Entity\Media;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/product', name: 'product')]
class ProductController extends AbstractController
{
    private Request $request;
    private SluggerInterface $slugger;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger, ValidatorInterface $validator)
    {
        $this->entityManager    = $entityManager;
        $this->slugger          = $slugger;
        $this->validator        = $validator;
    }

    #[Route('', name: '')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/get/{product}', name: '_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/new', name: '_new')]
    public function new(Request $request): Response
    {
        $product    = new Product();
        $form       = $this->createForm(ProductType::class, $product);
       
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $mediaFiles = $form->get('media')->getData();

            // Encode media files in base64
            $mediaEntities = [];
            foreach ($mediaFiles as $mediaFile) {
                $base64Content = base64_encode(file_get_contents($mediaFile->getPathname()));

                $media = new Media();
                $media->setMedia($base64Content);
                $media->setProduct($product);
                $mediaEntities[] = $media;
            }

            $errors = $this->validator->validate($product);

            if (count($errors) == 0) {
                foreach ($mediaEntities as $media) {
                    $this->entityManager->persist($media);
                }

                $product->setUser($this->getUser()); // Set the user
                $this->entityManager->persist($product);
                $this->entityManager->flush();

                return $this->redirectToRoute('user_profile', ['user' => $this->getUser()->getId()]);
            } else {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('product/create.html.twig', [
            'product'   => $product,
            'form'      => $form->createView(),
        ]);
    }
}
