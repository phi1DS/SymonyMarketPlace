<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormFactoryInterface;
use App\Form\UserApiType;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_create_user', methods: ['POST'])]
    public function create(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        // Decode JSON body
        $data = json_decode($request->getContent(), true);

        // Validate payload
        // Unicity check does not work with FormType :/, using validator + DTO more verbose but better
        $form = $formFactory->create(UserApiType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            return new JsonResponse([
                'errors' => (string) $form->getErrors(true, false)
            ], 400);
        }
        $cleanData = $form->getData();

        $user = new User();
        $user->setName($cleanData['name']);
        $user->setEmail($cleanData['email']);
        $user->setAddress($cleanData['address']);
        $entityManager->persist($user);
        $entityManager->flush();

        // serializing to json ?

        return new JsonResponse([
            'message' => 'User created',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
            ],
        ], Response::HTTP_CREATED);
    }

}
