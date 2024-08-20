<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\MainCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/categories/sub')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class CategoryController extends BaseController
{

    public function __construct(private readonly MainCategoryRepository $mainCategoryRepository)
    {
    }

    #[Route('/', name: "app_categories", methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): JsonResponse
    {
        if (null !== $permission = $this->checkUserAccess()) {
            return $permission;
        }
        $categories = $categoryRepository->findBy(['user' => $this->getUser()->getId()], ['name' => 'ASC']);
        return $this->jsonResponse(['categories' => $categories], context: [
            AbstractNormalizer::GROUPS => ['groups' => 'category:read']
        ]);
    }

    #[Route('/{id}', name: "app_category_show", methods: ['GET'])]
    public function show(int $id, CategoryRepository $categoryRepository): JsonResponse
    {
        $subCategory = $categoryRepository->findOneBy(['user' => $this->getUser()->getId(), 'id' => $id]);
        if (null !== $permission = $this->checkUserAccess($subCategory)) {
            return $permission;
        }
        return $this->jsonResponse(['main_category' => $subCategory], context: [
            AbstractNormalizer::GROUPS => ['groups' => 'category:read']
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $body = $request->getContent();
        $body = $this->prepareBodyOfTransaction($body);

        if ($main = $this->notFoundItemsResponse([$body->main])) {
            return $main;
        }

        $subCategory = new Category();
        $subCategory->setName($body->name);
        $subCategory->setColor($body->color);
        $subCategory->setMain($body->main);
        $subCategory->setUser($this->getUser());

        $entityManager->persist($subCategory);
        $entityManager->flush();

        return $this->jsonResponse(["message" => "Category \"$body->name\" created"], Response::HTTP_CREATED);
    }

    private function prepareBodyOfTransaction(string $body): stdClass
    {
        $body = $this->decodeJson($body);
        $body->main = $this->mainCategoryRepository->getRecordEntityFromUrl($body->main->url);
        return $body;
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'app_category_edit', methods: ['PATCH', 'PUT'])]
    public function edit(int $id, Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $subCategory = $categoryRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if (null !== $permission = $this->checkUserAccess($subCategory)) {
            return $permission;
        }
        $body = $request->getContent();
        $body = $this->prepareBodyOfTransaction($body);

        if (!is_null($subCategory)) {
            $subCategory->setName($body->name);
            $subCategory->setColor($body->color);
            $subCategory->setMain($body->main);

            $entityManager->persist($subCategory);
            $entityManager->flush();

            return $this->jsonResponse(["message" => "Category \"$body->name\" updated"]);
        }
        return $this->jsonResponse(["message" => "Category \"$body->name\" not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/delete/{id}', name: 'app_category_delete', methods: ['DELETE'])]
    public function delete(int $id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $subCategory = $categoryRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if (is_null($subCategory)) {
            return $this->jsonResponse(["message" => "Category is not found"], Response::HTTP_NOT_FOUND);
        }

        if (null !== $permission = $this->checkUserAccess($subCategory)) {
            return $permission;
        }

        try {
            $entityManager->beginTransaction();

            $entityManager->remove($subCategory);
            $entityManager->flush();
            $entityManager->commit();
        } catch (Exception $exception) {
            return $this->jsonResponse(["message" => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->jsonResponse(["message" => "Category \" {$subCategory->getName()} \" deleted."]);

    }

}