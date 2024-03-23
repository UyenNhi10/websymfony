<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderConfirmationController extends AbstractController
{
    #[Route('/order/confirmation', name: 'app_order_confirmation')]
    public function index(): Response
    {
        return $this->render('order_confirmation/index.html.twig', [
            'controller_name' => 'OrderConfirmationController',
        ]);
    }
}
