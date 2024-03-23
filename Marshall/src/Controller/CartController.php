<?php

namespace App\Controller;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class CartController extends AbstractController
{
    private $entityManager;
    private $security;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->logger = $logger;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        $userId = $user ? $user->getId() : null;
    
        // Lấy danh sách mục giỏ hàng dựa trên người dùng đã đăng nhập
        $cartItems = $this->entityManager->getRepository(Cart::class)->findCartItemsByUser($userId);
    
        $totalPrice = 0;
    
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->getProduct();
            $price = $product->getPrice();
            $quantity = $cartItem->getQuantity(); // Lấy số lượng của mục giỏ hàng
            $itemTotal = $price * $quantity; // Nhân giá với số lượng
            $totalPrice += $itemTotal;
        }
    
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cartItems' => $cartItems,
            'userId' => $userId,
            'totalPrice' => $totalPrice,
        ]);
    }
    

    #[Route('/cart/update/{id}', name: 'app_update_cart', methods: ['POST'])]
    public function updateCartItem(Request $request, Cart $cart): Response
    {
        $quantity = $request->request->getInt('quantity', 1);
        $cart->setQuantity($quantity);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{id}', name: 'app_delete_cart', methods: ['POST'])]
    public function deleteCartItem(Cart $cart): Response
    {
        $this->entityManager->remove($cart);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
    
    #[Route('/cart/add/{productId}', name: 'app_add_to_cart')]
    public function addToCart($productId): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('Sản phẩm không tồn tại.');
        }

        // Lấy thông tin người dùng đã đăng nhập
        $user = $this->security->getUser();

        // Tạo đối tượng giỏ hàng mới và gán giá trị
        $cart = new Cart();
        $cart->setProduct($product);
        $cart->setUser($user); // Gán đối tượng người dùng vào giỏ hàng

        // Lưu đối tượng giỏ hàng vào cơ sở dữ liệu
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_cart');
    }
   
    #[Route('/order', name: 'app_order_index', methods: ['GET'])]
    public function orderIndex(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();

        return $this->render('cart/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

   // CartController

   #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
   public function new(Request $request, OrderRepository $orderRepository, Security $security): Response
   {
       $user = $security->getUser();
       $userId = $user ? $user->getId() : null;
   
       // Lấy danh sách mục giỏ hàng dựa trên người dùng đã đăng nhập
       $cartItems = $this->entityManager->getRepository(Cart::class)->findCartItemsByUser($userId);
   
       $totalPrice = 0;
   
       foreach ($cartItems as $cartItem) {
           $product = $cartItem->getProduct();
           $price = $product->getPrice();
           $quantity = $cartItem->getQuantity(); // Retrieve the quantity of the cart item
           $itemTotal = $price * $quantity; // Multiply the price with the quantity
           $totalPrice += $itemTotal;
       }
   
       $order = new Order();
       $order->setUser($user); // Gán đối tượng người dùng vào đơn hàng
       $order->setTotal($totalPrice); // Gán giá trị total từ giỏ hàng vào đơn hàng
   
       $form = $this->createForm(OrderType::class, $order, ['hide_status' => true]);
       $form->handleRequest($request);
   
       if ($form->isSubmitted() && $form->isValid()) {
           $this->entityManager->beginTransaction();
   
           try {
               // Lưu đối tượng Order vào cơ sở dữ liệu
               $this->entityManager->persist($order);
               $this->entityManager->flush();
   
               // Lặp qua danh sách mục giỏ hàng và tạo OrderDetail cho mỗi mục giỏ hàng
               foreach ($cartItems as $cartItem) {
                   $product = $cartItem->getProduct();
                   $quantity = $cartItem->getQuantity();
   
                   $orderDetail = new OrderDetail();
                   $orderDetail->setProduct($product);
                   $orderDetail->setQuantity($quantity);
                   $orderDetail->setTotal($product->getPrice() * $quantity);
                   $orderDetail->setOrderid($order); // Truyền ID của đơn hàng vào OrderDetail
   
                   // Lưu đối tượng OrderDetail vào cơ sở dữ liệu
                   $this->entityManager->persist($orderDetail);
                   $this->entityManager->flush();
               }
   
               // Xóa các mục giỏ hàng của người dùng đã đăng nhập
               $this->entityManager->getRepository(Cart::class)->removeCartItemsByUser($userId);
   
               $this->entityManager->commit();
   
               return $this->redirectToRoute('app_order_success', [], Response::HTTP_SEE_OTHER);
           } catch (\Exception $e) {
               $this->entityManager->rollback();
               throw $e;
           }
       }
   
       return $this->renderForm('cart/order/new.html.twig', [
           'order' => $order,
           'form' => $form,
           'cartItems' => $cartItems,
           'totalPrice' => $totalPrice,
       ]);
   }


    #[Route('/order/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function deleteOrder(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order, true);
        }

        return $this->redirectToRoute('app_order_index');
    }
    #[Route('/ordersuccess', name: 'app_order_success')]
    public function index11(): Response
    {
        return $this->render('order_success/index.html.twig', [
            'controller_name' => 'OrderSuccessController',
        ]);
    }


    #[Route('/order/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function editOrder(Request $request, Order $order): Response
    {
        $form = $this->createForm(OrderType::class, $order, ['hide_status' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the changes to the order
            $this->entityManager->flush();

            return $this->redirectToRoute('app_order_index');
        }

        return $this->renderForm('cart/order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }
    #[Route('/orderstatus', name: 'app_order_status')]
    public function orderStatus(OrderRepository $orderRepository, UserInterface $user): Response
    {
        // Get the logged-in user's ID
        $userId = $user->getId();

        // Retrieve all orders with the given user ID
        $orders = $orderRepository->findBy(['user' => $userId]);

        return $this->render('orderstatus/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
