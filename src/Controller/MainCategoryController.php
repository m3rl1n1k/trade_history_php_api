<?php

namespace App\Controller;

use App\Entity\MainCategory;
use App\Repository\MainCategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/main/category')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class MainCategoryController extends BaseController
{

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route('/', name: "app_main_categories", methods: ['GET'])]
    public function index(MainCategoryRepository $mainCategoryRepository): JsonResponse
    {
        if (null !== $permission = $this->checkUserAccess()) {
            return $permission;
        }
        $mainCategory = $mainCategoryRepository->findBy(['user' => $this->getUser()->getId()], ['name' => 'ASC']);
        return $this->jsonResponse(['main_categories' => $mainCategory], context: [
            AbstractNormalizer::GROUPS => ['groups' => 'mainCategory:read']
        ]);
    }

    #[Route('/{id}', name: "app_main_category_show", methods: ['GET'])]
    public function show(int $id, MainCategoryRepository $mainCategoryRepository): JsonResponse
    {
        $mainCategory = $mainCategoryRepository->findOneBy(['user' => $this->getUser()->getId(), 'id' => $id]);
        if (null !== $permission = $this->checkUserAccess($mainCategory)) {
            return $permission;
        }
        return $this->jsonResponse(['main_category' => $mainCategory], context: [
            AbstractNormalizer::GROUPS => ['groups' => 'mainCategory:read']
        ]);
    }

    #[Route('/new', name: 'app_main_category_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $mainCategory = new MainCategory();

        $body = $request->getContent();
        $body = $this->prepareBodyOfTransaction($body);
        $mainCategory->setName($body['name']);
        $mainCategory->setColor($body['color']);
        $mainCategory->setUser($this->getUser());

        $entityManager->persist($mainCategory);
        $entityManager->flush();

        return $this->jsonResponse(["message" => "Main category is created"], Response::HTTP_CREATED);
    }

    private function prepareBodyOfTransaction(string $body)
    {
        $body = $this->decodeJson($body);
        $body['user'] = $this->userRepository->getRecordEntityFromUrl($body['user']['url']);
        return $body;
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'app_main_category_edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request, MainCategoryRepository $mainCategoryRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $mainCategory = $mainCategoryRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if (null !== $permission = $this->checkUserAccess($mainCategory)) {
            return $permission;
        }
        $body = $request->getContent();
        $body = $this->decodeJson($body);

        if ($body && !is_null($mainCategory)) {
            $mainCategory->setName($body['name']);
            $mainCategory->setColor($body['color']);

            $entityManager->persist($mainCategory);
            $entityManager->flush();

            return $this->jsonResponse(["message" => "Main category is updated"]);
        }
        return $this->jsonResponse(["message" => "Main category is not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/delete/{id}', name: 'app_main_category_delete', methods: ['DELETE'])]
    public function delete(int $id, MainCategoryRepository $mainCategoryRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $mainCategory = $mainCategoryRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if (is_null($mainCategory)) {
            return $this->jsonResponse(["message" => "Main category is not found"], Response::HTTP_NOT_FOUND);
        }

        if (null !== $permission = $this->checkUserAccess($mainCategory)) {
            return $permission;
        }

        try {
            $entityManager->beginTransaction();

            $entityManager->remove($mainCategory);
            $entityManager->flush();
            $entityManager->commit();
        } catch (Exception $exception) {
            return $this->jsonResponse(["message" => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->jsonResponse(["message" => "Main category is deleted."]);

    }

}