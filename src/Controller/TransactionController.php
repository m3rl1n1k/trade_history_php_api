<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Paginator\PaginationFactory;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use App\Repository\WalletRepository;
use App\Service\CalculationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use stdClass;
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
    protected string $flag;

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
        $transaction = $transactionRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

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
    public function new(Request $request, EntityManagerInterface $entityManager, CalculationService $calculationService): JsonResponse
    {
        $body = $this->prepareBodyOfTransaction($request->getContent());

        if ($response = $this->notFoundItemsResponse([$body->category, $body->wallet])) {
            return $response;
        }
        $transaction = new Transaction();
        $transaction->setUser($this->getUser());
        $transaction = $transaction->setDataForTransaction($transaction, $body);

        $calculationService->calculate($body->wallet, $transaction, ['flag' => $body->flag]);

        $entityManager->persist($transaction);
        $entityManager->flush();

        return $this->jsonResponse(["message" => "Transaction is created"], Response::HTTP_CREATED);
    }

    private function prepareBodyOfTransaction(string $body): stdClass
    {
        $body = $this->decodeJson($body);
        $body->wallet = $this->walletRepository->getRecordEntityFromUrl($body->wallet->url);
        $body->category = $this->categoryRepository->getRecordEntityFromUrl($body->category->url);
        $this->flag = $body->flag;
        return $body;
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'app_transaction_edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request, TransactionRepository $transactionRepository, EntityManagerInterface $entityManager, CalculationService $calculationService): JsonResponse
    {
        $body = $request->getContent();
        $body = $this->prepareBodyOfTransaction($body);

        $transaction = $transactionRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if (null !== $permission = $this->checkUserAccess($transaction)) {
            return $permission;
        }
        if (!is_null($transaction)) {
            if ($response = $this->notFoundItemsResponse([$body->category, $body->wallet])) {
                return $response;
            }
            $oldAmount = $transaction->getAmount();

            $transaction = $transaction->setDataForTransaction($transaction, $body);
            $calculationService->calculate($body->wallet, $transaction, [
                'flag' => $body->flag,
                'old_amount' => $oldAmount,
            ]);
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->jsonResponse(["message" => "Transaction is updated"]);
        }

        return $this->jsonResponse(["message" => "Transaction is not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/delete/{id}', name: 'app_transaction_delete', methods: ['DELETE'])]
    public function delete(int $id, TransactionRepository $transactionRepository, EntityManagerInterface $entityManager, CalculationService $calculationService): JsonResponse
    {
        $transaction = $transactionRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if (is_null($transaction)) {
            return $this->jsonResponse(["message" => "Transaction is not found"], Response::HTTP_NOT_FOUND);
        }

        if (null !== $permission = $this->checkUserAccess($transaction)) {
            return $permission;
        }
        try {
            $entityManager->beginTransaction();
            $calculationService->calculate($transaction->getWallet(), $transaction, ['flag' => 'remove']);
            $entityManager->remove($transaction);
            $entityManager->flush();
            $entityManager->commit();
        } catch (Exception $exception) {
            return $this->jsonResponse(["message" => "{$exception->getMessage()} {$exception->getFile()}"], Response::HTTP_BAD_REQUEST);
        }

        return $this->jsonResponse(["message" => "Transaction is deleted."]);
    }


}