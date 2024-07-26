<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/user')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class UserController extends BaseController
{
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        return $this->jsonResponse(['user' => $user], context: [
            AbstractNormalizer::GROUPS => ['groups' => 'user:read'],
        ]);
    }

    #[Route('/change-password', name: 'app_user_change_password', methods: ['POST'])]
    public function changePassword(Request $request, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        return $this->jsonResponse(['user' => $user], context: [
            AbstractNormalizer::GROUPS => ['groups' => 'user:read'],
        ]);
    }

    #[Route('/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $body = $request->getContent();
        $body = $this->decodeJson($body);
        $email = $body['secret_phrase'];
        if ($email !== $this->getUser()->getUserIdentifier()) {
            return $this->jsonResponse(['message' => 'Not right secret phrase'], 401);
        }
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $entityManager->beginTransaction();
        $entityManager->remove($user);
        $entityManager->flush();
        $entityManager->commit();
        return $this->jsonResponse(['message' => "User is successfully removed"]);
    }
}
