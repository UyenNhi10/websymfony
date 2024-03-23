<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;

class NewController extends AbstractController
{
    #[Route('/postnew', name: 'app_new')]
     public function index2(PostRepository $postRepository): Response
    {
        $products = $postRepository->findAll(); 

        return $this->render('new/index.html.twig', [
            'products' => $products,
        ]);
    }
}
