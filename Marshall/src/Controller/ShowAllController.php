<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowAllController extends AbstractController
{
    #[Route('/show_all', name: 'show_all')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll(); // Lấy tất cả sản phẩm
        return $this->render('show_all/index.html.twig', [
            'products' => $products, // Truyền biến products vào template
        ]);
    }
}
