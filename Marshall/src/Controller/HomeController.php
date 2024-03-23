<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Order;
use App\Repository\OrderRepository;



class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }

    #[Route('/home', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], [], 8);
        $headphonesProducts = $this->getProductsByCategory('Headphones', 4);
        $travelProducts = $this->getProductsByCategory('Travel', 4);
        $accessoriesProducts = $this->getProductsByCategory('Accessories', 4);
        $speakersProducts = $this->getProductsByCategory('Speakers', 4);

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'headphonesProducts' => $headphonesProducts,
            'travelProducts' => $travelProducts,
            'accessoriesProducts' => $accessoriesProducts,
            'speakersProducts' => $speakersProducts,

        ]);
    }
    private function getProductsByCategory(string $categoryName): array
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);
        if (!$category) {
            return []; // Return an empty array if category not found
        }

        return $this->entityManager->getRepository(Product::class)->findBy(['category' => $category]);
    }
    #[Route('/home1', name: 'app_home_all')]
    public function index2(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();  // Lấy 6 sản phẩm từ repository

        return $this->render('home/index.html1.twig', [
            'products' => $products,
        ]);
    }


    #[Route('/speakers', name: 'app_speakers')]
    public function speakersIndex(ProductRepository $productRepository): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Speakers']);
        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('home/speakers/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        $keyword = $request->query->get('keyword');

        $products = $productRepository->searchByKeyword($keyword);

        return $this->render('result/index.html.twig', [
            'products' => $products,
            'keyword' => $keyword,
        ]);
    }

    #[Route('/accessories', name: 'app_accessories')]
    public function accessoriesIndex(ProductRepository $productRepository): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Accessories']);
        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('home/accessories/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/accessories/{id}', name: 'app_accessories_product_detail')]
    public function accessoriesProductDetail(Product $product): Response
    {
        return $this->render('product_detail/index.html.twig', [
            'product' => $product,
        ]);
    }
    #[Route('/travel', name: 'app_travel')]

    public function travelIndex(ProductRepository $productRepository): Response
    {
        // Lấy danh sách sản phẩm của category "travel"
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Travel']);
        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('home/travel/index.html.twig', [
            'products' => $products,
        ]);
    }
    #[Route('/headphones', name: 'app_headphones')]

    public function headphonesindex(ProductRepository $productRepository): Response
    {
        // Lấy danh sách sản phẩm của category "headphones"
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Headphones']);
        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('home/headphones/index.html.twig', [
            'products' => $products,
        ]);
    }

}
