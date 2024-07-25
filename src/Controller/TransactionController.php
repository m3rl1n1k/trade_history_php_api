<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Paginator\PaginationFactory;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/transaction')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class TransactionController extends BaseController
{
    public function __construct(
        protected readonly WalletRepository   $walletRepository,
        protected readonly CategoryRepository $categoryRepository,
        protected readonly UserRepository     $userRepository,
        protected SerializerInterface         $serializer,
    )
    {
    }

    #[Route('/', name: 'app_transactions', methods: ['GET'])]
    public function index(Request $request, TransactionRepository $transactionRepository, PaginationFactory $paginationFactory): JsonResponse
    {
        if (null !== $permission = $this->checkUserAccess()) {
            return $permission;
        }

        $transactions = $transactionRepository->findByGetQuery($this->getUser()->getId());
        $transactions = $paginationFactory->createPagination($transactions, $request);

        return $this->jsonResponse(['transactions' => $transactions], context: [
            AbstractNormalizer::GROUPS => ['groups' => 'transaction:read']
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_show', methods: ['GET'])]
    public function show(int $id, TransactionRepository $transactionRepository): JsonResponse
    {
        $transaction = $transactionRepository->findOneBy(['id' => $id]);

        if (null !== $permission = $this->checkUserAccess($transaction)) {
            return $permission;
        }

        return $this->jsonResponse(['transaction' => $transaction], context: [
            AbstractNormalizer::GROUPS => ['groups' => 'transaction:read']
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/new', name: 'app_transaction_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        $body = $request->getContent();
        $body = $this->prepareBodyOfTransaction($body);

        if (is_string($body['wallet']) || is_string($body['category'])) {
            return $this->jsonResponse(['message' => "Record not found", "items" => [
                $body['wallet'],
                $body['category']
            ]], Response::HTTP_NOT_FOUND);
        }

        $transaction = new Transaction();
        $transaction->setUser($this->getUser());
        $transaction = $transaction->setDataForTransaction($transaction, $body);

        $entityManager->persist($transaction);
        $entityManager->flush();

        return $this->jsonResponse(['message' => "Transaction is created"], Response::HTTP_CREATED);
    }

    private function prepareBodyOfTransaction(string $body)
    {
        $body = $this->decodeJson($body);
        $body['wallet'] = $this->walletRepository->getRecordEntityFromUrl($body['wallet']['url']);
        $body['category'] = $this->categoryRepository->getRecordEntityFromUrl($body['category']['url']);
        return $body;
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'app_transaction_edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request, TransactionRepository $transactionRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $body = $request->getContent();
        $transaction = $transactionRepository->findOneBy(['id' => $id]);

        if (null !== $permission = $this->checkUserAccess($transaction)) {
            return $permission;
        }

        if ($body && !is_null($transaction)) {

            $body = $this->prepareBodyOfTransaction($body);
            $transaction = $transaction->setDataForTransaction($transaction, $body);

            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->jsonResponse(['message' => "Transaction is updated"]);
        }

        return $this->jsonResponse(['message' => "Transaction is not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/delete/{id}', name: 'app_transaction_delete', methods: ['DELETE'])]
    public function delete(int $id, TransactionRepository $transactionRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $transaction = $transactionRepository->findOneBy(['id' => $id]);

        if (is_null($transaction)) {
            return $this->jsonResponse(['message' => "Transaction is not found"], Response::HTTP_NOT_FOUND);
        }

        if (null !== $permission = $this->checkUserAccess($transaction)) {
            return $permission;
        }
        try {
            $entityManager->beginTransaction();

            $entityManager->remove($transaction);
            $entityManager->flush();
            $entityManager->commit();
        } catch (Exception $exception) {
            return $this->jsonResponse(['message' => "{$exception->getMessage()}"], Response::HTTP_BAD_REQUEST);
        }

        return $this->jsonResponse(['message' => "Transaction is deleted."]);
    }


}