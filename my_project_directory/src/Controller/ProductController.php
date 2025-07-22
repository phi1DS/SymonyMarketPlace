<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;

final class ProductController extends AbstractController
{
    // protected by firewall, user must be logged in
    #[Route('/product/create', name: 'app_product_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $product->setUser($user);

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_account');
        }

        return $this->render('product/create.html.twig', [
            'createProductFrom' => $form,
        ]);
    }
}
