<?php

namespace App\Controller\Account;

use App\Entity\Wallet;
use App\Form\WalletType;
use App\Repository\UserRepository;
use App\Repository\WalletRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/account/wallet')]
class WalletController extends AbstractController
{
    #[Route('/', name: 'app_wallet_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userRepository = $userRepository->find($this->getUser());
        $wallet = $userRepository->getWallet();

        return $this->render('wallet/index.html.twig', [
            'wallet' => $wallet,
        ]);
    }
}
