<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class StoreController extends AbstractController
{
    #[Route('/store', name: 'app_store')]
    public function index(Request $request ,ProductsRepository $productRepository)
    {
        $searchQuery = $request->query->get('search');
        if ($searchQuery) {
            $products = $productRepository->findBySearchQuery($searchQuery);
        } else {
            $products = $productRepository->findAll();
        }
        $product = new Products();
       
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        $produit = $form->getData();
        //****************Manage Uploaded FileName
        $photo_prod = $form->get('image')->getData();
        $originalFilename = $photo_prod->getClientOriginalName();
        $newFilename = $originalFilename.'-'.uniqid().'.'.$photo_prod->getClientOriginalExtension();
        $photo_prod->move($this->getParameter('images_directory'),$newFilename);
        $product->setPhoto($newFilename);
        //****************
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
        //return $this->redirectToRoute('confirm');
        }
        return $this->render('store/index.html.twig', [
            'products' => $products,
        ]);
    }

    
    #[Route('/{id}', name: 'app_store_show', methods: ['GET'])]
    public function show(Products $product): Response
    {
        return $this->render('store/show.html.twig', [
            'product' => $product,
        ]);
    }
   /**
     * @Route("/add1/{id}", name="app_add1")
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
        return $this->redirectToRoute('app_store');
    }
    

    
}
