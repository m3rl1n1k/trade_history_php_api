<?php

namespace App\Controller;

use App\Repository\MainCategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/main/category')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class MainCategoryController extends BaseController
{
    #[Route('/', name: "app_main_categories", methods: ['GET'])]
    public function index(MainCategoryRepository $mainCategoryRepository): JsonResponse
    {
        $this->checkPermission();
        $mainCategory = $mainCategoryRepository->findBy(['user' => $this->getUser()->getId()], ['name' => 'ASC']);
        return $this->jsonResponse($mainCategory, context: [
            AbstractNormalizer::GROUPS => ['groups' => 'mainCategory:read']
        ]);
    }

    #[Route('/{id}', name: "app_main_category_show", methods: ['GET'])]
    public function show(int $id, MainCategoryRepository $mainCategoryRepository): JsonResponse
    {
        $mainCategory = $mainCategoryRepository->findOneBy(['user' => $this->getUser()->getId(), 'id' => $id], ['name' => 'ASC']);
        $this->checkPermission($mainCategory);
        return $this->jsonResponse($mainCategory, context: [
            AbstractNormalizer::GROUPS => ['groups' => 'mainCategory:read']
        ]);
    }

}