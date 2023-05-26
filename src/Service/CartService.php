<?php 

Namespace App\Service;

use App\Entity\Configurator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {

    private RequestStack $requestStack; 
    private EntityManagerInterface $em; 

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack; 
        $this->em = $em; 
    }

    public function addToCart(int $id) : void {
        $cart = $this->requestStack->getSession()->get('cart', []); 
        if(!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1; 
        }
        $this->getSession()->set('cart', $cart); 
    }

    public function removeToCart(int $id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []); 
        unset($cart[$id]); 
        return $this->getSession()->set('cart', $cart);
    }

    public function removeCartAll() 
    {
        return $this->getSession()->remove('cart'); 
    }

    public function getTotal() : array
    {
        $cart = $this->getSession()->get('cart'); 
        $cartData = []; 
        
        if ($cart) {
            foreach ($cart as $id => $quantity) {
                $configurator = $this->em->getRepository(Configurator::class)->findOneBy(['id' => $id]);             
                if (!$configurator) {
                    // Supprimer produit. 
                }
                $cartData[] = [
                    'configurator' => $configurator, 
                    'quantity' => $quantity
                ]; 
            }
        }
        
        return $cartData; 
    }

    private function getSession() {
        return $this->requestStack->getSession(); 
    }
}