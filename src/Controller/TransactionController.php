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
        protected WalletRepository    $walletRepository,
        protected CategoryRepository  $categoryRepository,
        protected UserRepository      $userRepository,
        protected SerializerInterface $serializer,
    )
    {
    }

    #[Route('/', name: 'app_transactions', methods: ['GET'])]
    public function index(Request $request, TransactionRepository $transactionRepository, PaginationFactory $paginationFactory): JsonResponse
    {
//        $transactions = $transactionRepository->findBy(['user' => $this->getUser()->getId()]);
        $transactions = $transactionRepository->findByGetQuery($this->getUser()->getId());
        $transactions = $paginationFactory->createPagination($transactions, $request);
        return $this->jsonResponse($transactions, context: [
            AbstractNormalizer::GROUPS => ['groups' => 'transaction:read']
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_show', methods: ['GET'])]
    public function show(int $id, TransactionRepository $transactionRepository): JsonResponse
    {
        $transaction = $transactionRepository->findOneBy(['id' => $id]);
        $this->checkPermission($transaction);
        return $this->jsonResponse($transaction, context: [
            AbstractNormalizer::GROUPS => ['groups' => 'transaction:read']
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/new', name: 'app_transaction_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // I don't know at this moment how to deserialize and update entity
        $transaction = new Transaction();

        $body = $request->getContent();
        $body = $this->prepareBody($body);

        $transaction->setUser($this->getUser());
        $transaction = $transaction->setDataForTransaction($transaction, $body);

        $entityManager->persist($transaction);
        $entityManager->flush();

        return $this->jsonResponse("Transaction is created", Response::HTTP_CREATED);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'app_transaction_edit', methods: ['GET', 'PUT'])]
    public function edit(int $id, Request $request, TransactionRepository $transactionRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $body = $request->getContent();
        $transaction = $transactionRepository->findOneBy(['id' => $id]);

        $this->checkPermission($transaction);

        if ($body) {

            $body = $this->prepareBody($body);
            $transaction = $transaction->setDataForTransaction($transaction, $body);

            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->jsonResponse("Transaction is updated");
        }

        return $this->jsonResponse($transaction, context: [
            AbstractNormalizer::GROUPS => ['groups' => 'transaction:read']
        ]);
    }

    private function prepareBody(string $body)
    {
        $body = $this->decodeJson($body);

        $body['wallet'] = $this->walletRepository->getRecordEntityFromUrl($body['wallet']['url']);
        $body['category'] = $this->categoryRepository->getRecordEntityFromUrl($body['category']['url']);

        return $body;
    }

}