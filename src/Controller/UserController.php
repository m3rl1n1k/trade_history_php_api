<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
