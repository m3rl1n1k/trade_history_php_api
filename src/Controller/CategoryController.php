<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/category')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class CategoryController extends BaseController
{
    #[Route('/', name: "app_categories", methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): JsonResponse
    {
        $this->checkPermission();
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
        return $this->jsonResponse($categories);
    }

    #[Route('/{id}', name: "app_category_show", methods: ['GET'])]
    public function show(int $id, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->findOneBy(['user' => $this->getUser()->getId(), 'id' => $id], ['name' => 'ASC']);
        $this->checkPermission($category);
        return $this->jsonResponse($category);
    }

}