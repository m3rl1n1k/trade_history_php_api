<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    #[Route('/registration', name: 'app_registration', methods: ['POST'])]
    public function registration(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $body = json_decode($request->getContent());

        if ($this->userRepository->findBy(['email' => $body->email])) {
            return $this->json(["message" => "Email is taken"], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($body->email);

        $password = $passwordHasher->hashPassword($user, $body->password);
        $user->setPassword($password);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(["message" => "You registered successfully"], Response::HTTP_CREATED);
    }
}
