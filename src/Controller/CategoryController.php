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
        if (null !== $permission = $this->checkUserAccess()) {
            return $permission;
        }
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
        return $this->jsonResponse(['categories' => $categories]);
    }

    #[Route('/{id}', name: "app_category_show", methods: ['GET'])]
    public function show(int $id, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->findOneBy(['id' => $id]);
        if (null !== $permission = $this->checkUserAccess($category)) {
            return $permission;
        }
        return $this->jsonResponse(['category' => $category]);
    }

}