<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;


class MainController extends AbstractController
{
    #[Route('/', name:'app_homepage')]
    public function homepage(EntityManagerInterface $entityManager): Response
    {
        // display all products
        $products = $entityManager->getRepository(Product::class)->findBy([], [], 10); // add paging system

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
        ]);
    }

    #[Route('/testmail', name:'app_test_mail')]
    public function testEmail(TransportInterface $mailer)
    {
        $email = (new Email())
            ->from('you@example.com')
            ->to('test@example.com')
            ->subject('Test Email')
            ->text('This is a test.');

        try {
            $mailer->send($email);
            
            return new Response('Email sent');
        } catch (TransportExceptionInterface $e) {
            return new Response('Error: ' . $e->getMessage());
        }

        return new Response('Email ?.');
    }
}
