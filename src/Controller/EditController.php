<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Products;
use App\Form\ProductsType;

class EditController extends AbstractController
{
    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Request $request, Products $produit): Response
    {
        $form = $this->createForm(ProductsType::class, $produit, [
            'photo_value' => $produit->getImage(), // <--- passer la valeur actuelle de la photo du produit
        ]);
   
        
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
       {
            $produit = $form->getData();
                        // Check if the photo field has a value
                        $photo = $form->get('image')->getData();
                        if ($photo) {
                            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                            $newFilename = $originalFilename.'-'.uniqid().'.'.$photo->getClientOriginalExtension();
                            $photo->move(
                                $this->getParameter('images_directory'),
                                $newFilename
                            );
                            $produit->setImage($newFilename);
                        }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }
}