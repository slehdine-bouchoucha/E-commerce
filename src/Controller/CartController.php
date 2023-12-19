<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Products;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
{
    // Retrieve the cart items from the session
    $cartItems = $request->getSession()->get('cart_items', []);

    // Create an array to store the cart items with quantity
    $cart = [];

    // Count the quantity of each item in the cart
    foreach ($cartItems as $itemId) {
        if (!isset($cart[$itemId])) {
            $cart[$itemId] = 0;
        }
        $cart[$itemId]++;
    }

    $totalPrice = 0;
    $entityManager = $this->getDoctrine()->getManager();
    $articles = $entityManager->getRepository(Products::class)->findBy(['id' => array_keys($cart)]);

    // Calculate the total price by iterating over the products and quantities in the cart
    foreach ($articles as $article) {
        $totalPrice += $article->getPrix() * $cart[$article->getId()];
    }

    return $this->render('cart/index.html.twig', ['articles' => $articles, 'cart' => $cart, 'totalPrice' => $totalPrice]);
}


    /**
     * @Route("/add/{id}", name="app_add")
     */
    public function add($id, Request $request)
    {
        // Retrieve the cart items from the session
        $cartItems = $request->getSession()->get('cart_items', []);

        // Add the new item to the cart items array
        $cartItems[] = $id;

        // Store the updated cart items in the session
        $request->getSession()->set('cart_items', $cartItems);

        // Redirect to the cart page
        return $this->redirectToRoute('app_cart');
    }
    /**
 * @Route("/delete/{id}", name="app_delete")
 */
public function delete($id, Request $request)
{
    // Retrieve the cart items from the session
    $cartItems = $request->getSession()->get('cart_items', []);

    // Find the index of the item to delete
    $index = array_search($id, $cartItems);

    // If the item is found, remove it from the cart items array
    if ($index !== false) {
        unset($cartItems[$index]);
    }

    // Store the updated cart items in the session
    $request->getSession()->set('cart_items', $cartItems);

    // Redirect back to the cart page
    return $this->redirectToRoute('app_cart');
}

} 