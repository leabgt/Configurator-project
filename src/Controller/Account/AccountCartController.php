<?php

namespace App\Controller\Account;

use App\Entity\Cart;
use App\Form\Cart1Type;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/account/cart')]
class AccountCartController extends AbstractController
{
    #[Route('/', name: 'app_account_cart_index', methods: ['GET'])]
    public function index(CartRepository $cartRepository, UserRepository $userRepository, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userRepository = $userRepository->find($this->getUser());
        $carts = $userRepository->getCarts();

        return $this->render('account_cart/index.html.twig', [
            'carts' => $carts,
        ]);
    }

    #[Route('/new', name: 'app_account_cart_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CartRepository $cartRepository): Response
    {
        $cart = new Cart();
        $form = $this->createForm(Cart1Type::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartRepository->save($cart, true);

            return $this->redirectToRoute('app_account_cart_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account_cart/new.html.twig', [
            'cart' => $cart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_account_cart_show', methods: ['GET'])]
    public function show(Cart $cart): Response
    {
        return $this->render('account_cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_account_cart_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cart $cart, CartRepository $cartRepository): Response
    {
        $form = $this->createForm(Cart1Type::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartRepository->save($cart, true);

            return $this->redirectToRoute('app_account_cart_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account_cart/edit.html.twig', [
            'cart' => $cart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_account_cart_delete', methods: ['POST'])]
    public function delete(Request $request, Cart $cart, CartRepository $cartRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cart->getId(), $request->request->get('_token'))) {
            $cartRepository->remove($cart, true);
        }

        return $this->redirectToRoute('app_account_cart_index', [], Response::HTTP_SEE_OTHER);
    }
}
