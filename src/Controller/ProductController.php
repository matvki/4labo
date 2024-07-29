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
    private SluggerInterface        $slugger;
    private ValidatorInterface      $validator;
    private EntityManagerInterface  $entityManager;

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
            $mediaFiles     = $form->get('media')->getData();
            $mediaEntities  = [];

            // Encode media files in base64
            foreach ($mediaFiles as $mediaFile) {
                $base64Content  = base64_encode(file_get_contents($mediaFile->getPathname()));
                $media          = new Media();

                $media->setMedia($base64Content);
                $media->setProduct($product);
                $mediaEntities[] = $media;
            }

            $errors = $this->validator->validate($product);

            if (count($errors) == 0) {
                foreach ($mediaEntities as $media)
                    $this->entityManager->persist($media);

                $product->setUser($this->getUser()); // Set the user

                try {
                    $this->entityManager->persist($product);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                }

                return $this->redirectToRoute('user_profile', ['user' => $this->getUser()->getId()]);
            } else {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('product/create.html.twig', [
            'product'   => $product,
            'title'     => 'Nouveau produit',
            'form'      => $form->createView(),
        ]);
    }

    #[Route('/edit/{product}', name: '_edit')]
    public function edit(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $mediaFiles     = $form->get('media')->getData();
            $mediaEntities  = [];

            // Encode media files in base64
            foreach ($mediaFiles as $mediaFile) {
                $base64Content  = base64_encode(file_get_contents($mediaFile->getPathname()));
                $media          = new Media();

                $media->setMedia($base64Content);
                $media->setProduct($product);
                $mediaEntities[] = $media;
            }

            $errors = $this->validator->validate($product);

            if (count($errors) == 0) {
                foreach ($mediaEntities as $media)
                    $this->entityManager->persist($media);

                $product->setUser($this->getUser()); // Set the user

                try {
                    $this->entityManager->persist($product);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                }

                return $this->redirectToRoute('user_profile', ['user' => $this->getUser()->getId()]);
            } else {
                foreach ($errors as $error)
                    $this->addFlash('error', $error->getMessage());
            }
        }


        return $this->render('product/create.html.twig', [
            'product' => $product,
            'title'   => 'Modifier le produit',
            'update'  => true,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/delete/{product}', name: '_delete')]
    public function delete(Product $product): Response
    {
        try {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('user_profile', ['user' => $this->getUser()->getId()]);
    }
}
