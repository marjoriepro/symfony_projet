<?php

namespace App\Controller\front;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

Class CommandController extends AbstractController
{
    #[Route('/front/cart/add/{id}', name: 'add_cart')]

    public function addCart($id, SessionInterface $sessionInterface)
    {
        $cart = $sessionInterface->get('cart', []);
        if(!empty($cart[$id])){
            $cart[$id]++;

        }else{
            $cart[$id] = 1;
        }
        
        $sessionInterface->set('cart', $cart);

        return $this->redirectToRoute('show_product', ['id' => $id]);
    }

    #[Route('front/cart', name: "show_cart")]

    public function showCart(SessionInterface $sessionInterface, ProductRepository $productRepository)
    {
        $cart = $sessionInterface->get('cart', []);
        $cartWithData = [];

        foreach($cart as $id => $quantity)
        {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return $this-> render('front/cart.html.twig', ['items' => $cartWithData]);
    }


    #[Route('front/cart/delete/{id}', name:'delete_cart')]

    public function deleteCart($id, SessionInterface $sessionInterface)
    {
        $cart = $sessionInterface->get('cart', []);
        if(!empty($cart[$id]) && $cart == 1)
        {
            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }
        $sessionInterface->set('cart', $cart);

        return $this->redirectToRoute('show_cart');
    }
}


