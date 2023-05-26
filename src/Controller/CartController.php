<?php

namespace App\Controller;

use Datetime;
use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/mon-panier')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart_index')]
    public function index(CartService $cartService, Request $request, CartRepository $cartRepository): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal(),
        ]);
    }

    #[Route('/add/{id<\d+>}', name: 'app_cart_add')]
    public function addToCart(CartService $cartService, int $id): Response
    {
        $cartService->addToCart($id);
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove/{id<\d+>}', name: 'app_cart_remove')]
    public function removeToCart(CartService $cartService, int $id): Response
    {
        $cartService->removeToCart($id);
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/add/removeAll', name: 'app_cart_removeAll')]
    public function removeAll(CartService $cartService): Response
    {
        $cartService->removeCartAll();
        return $this->redirectToRoute('app_cart_index');
    }


    #[Route('/new', name: 'app_cart_new')]
    public function new(Request $request, CartService $cartService, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        /// CART /// 
        $cart = new Cart();
        $data = $cartService->getTotal();
        foreach ($data as $item) {
            $configurator = $item['configurator'];
            $cart->addConfigurator($configurator);
        }

        $currentDatetime = new Datetime();
        $cart->setDate($currentDatetime);

        $user = $userRepository->find($this->getUser());
        $user->addCart($cart);

        $em->persist($cart);
        $em->flush();

        /// WALLET ///
        $wallet = $user->getWallet();

        $cartTotalPrice = 0;
        foreach ($data as $item) {
            $configurator = $item['configurator'];
            $price = $configurator->getTotalPrice();
            $cartTotalPrice += $price;
        }
        $amountAddToWallet = $cartTotalPrice * 0.05;

        $currentAmount = $wallet->getAmount();
        $newAmount = $currentAmount += $amountAddToWallet;
        $wallet->setAmount($newAmount);

        $em->persist($wallet);
        $em->flush();

        /// REMOVE CART ///
        $cartService->removeCartAll();
        $cartID = $cart->getId();

        return $this->redirectToRoute('app_cart_show', ['id' => $cartID]);
    }

    #[Route('/{id}', name: 'app_cart_show', methods: ['GET'])]
    public function show(Cart $cart): Response
    {
        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_cart_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Cart $cart, CartRepository $cartRepository): Response
    // {
    //     $form = $this->createForm(CartType::class, $cart);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $cartRepository->save($cart, true);

    //         return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('cart/edit.html.twig', [
    //         'cart' => $cart,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_cart_delete', methods: ['POST'])]
    // public function delete(Request $request, Cart $cart, CartRepository $cartRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->request->get('_token'))) {
    //         $cartRepository->remove($cart, true);
    //     }

    //     return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    // }
}
