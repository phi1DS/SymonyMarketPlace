<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function account(EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // display products created by the user

        $userProducts = $entityManager->getRepository(Product::class)->findByUser($user);

        // option to delete them or create an new one

        // on main page, display all products with their owner

        return $this->render('account.html.twig', [
            'userIdentifier' => $user->getEmail(),
            'products' => $userProducts,
        ]);
    }
}
