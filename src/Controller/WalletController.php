<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/wallets')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class WalletController extends BaseController
{
    public function __construct(protected SettingService $settingService)
    {
    }

    #[Route('/', name: 'app_wallets', methods: ['GET'])]
    public function index(WalletRepository $walletRepository): JsonResponse
    {
        if (null !== $permission = $this->checkUserAccess()) {
            return $permission;
        }
        $wallets = $walletRepository->findBy(['user' => $this->getUser()->getId()]);
        return $this->jsonResponse(['wallets' => $wallets], context: [
            AbstractNormalizer::GROUPS => ['wallet:read']
        ]);
    }

    #[Route('/{id}', name: 'app_wallet_show', methods: ['GET'])]
    public function show(int $id, WalletRepository $walletRepository): JsonResponse
    {

        $wallet = $walletRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if (null !== $permission = $this->checkUserAccess($wallet)) {
            return $permission;
        }
        return $this->jsonResponse(['wallet' => $wallet], context: [
            AbstractNormalizer::GROUPS => ['wallet:read']
        ]);
    }

    #[Route('/new', name: 'app_wallet_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (is_null($user->getSetting())) {
            return $this->jsonResponse(["message" => "Generate first setting!"], Response::HTTP_BAD_REQUEST);
        }

        $wallet = new Wallet();

        $body = $request->getContent();
        $body = $this->decodeJson($body);

        $currency = $body->currency ?? $this->settingService->currencyDefaultUser();

        $wallet->setUser($this->getUser());
        $wallet->setAmount($body->amount);
        $wallet->setCurrency($currency);
        $wallet->setCardName($body->card_name);
        $wallet->setNumber($currency);


        $entityManager->persist($wallet);
        $entityManager->flush();

        return $this->jsonResponse(["message" => "Wallet is created"], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'app_wallet_edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request, WalletRepository $walletRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $wallet = $walletRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if (null !== $permission = $this->checkUserAccess($wallet)) {
            return $permission;
        }
        $body = $request->getContent();
        $body = $this->decodeJson($body);

        $currency = $body->currency ?? $this->settingService->currencyDefaultUser();

        if (!is_null($wallet)) {
            $wallet->setAmount($body->amount);
            $wallet->setCurrency($currency);
            $wallet->setCardName($body->card_name);

            $entityManager->persist($wallet);
            $entityManager->flush();

            return $this->jsonResponse(["message" => "Wallet is updated"]);
        }
        return $this->jsonResponse(["message" => "Wallet is not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/delete/{id}', name: 'app_wallet_delete', methods: ['DELETE'])]
    public function delete(int $id, WalletRepository $categoryRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $wallet = $categoryRepository->findOneBy(['id' => $id, 'user' => $this->getUser()->getId()]);

        if (is_null($wallet)) {
            return $this->jsonResponse(["message" => "Wallet is not found"], Response::HTTP_NOT_FOUND);
        }

        if (null !== $permission = $this->checkUserAccess($wallet)) {
            return $permission;
        }

        try {
            $entityManager->beginTransaction();

            $entityManager->remove($wallet);
            $entityManager->flush();
            $entityManager->commit();
        } catch (Exception $exception) {
            return $this->jsonResponse(["message" => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->jsonResponse(["message" => "Wallet is deleted."]);

    }


}