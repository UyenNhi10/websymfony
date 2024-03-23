<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;

class DetailnewController extends AbstractController
{
    #[Route('/postdetail/{id}', name: 'app_detailnew', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('detailnew/index.html.twig', [
            'product' => $post,
        ]);
        
    }
}
