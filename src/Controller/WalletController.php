<?php

namespace App\Controller;

use App\Repository\WalletRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wallet')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class WalletController extends BaseController
{
    #[Route('/', name: 'app_wallets', methods: ['GET'])]
    public function index(WalletRepository $walletRepository): JsonResponse
    {

        $this->checkPermission();
        $wallets = $walletRepository->findBy(['user' => $this->getUser()->getId()]);
        return $this->jsonResponse($wallets);
    }

    #[Route('/{id}', name: 'app_wallet_show', methods: ['GET'])]
    public function show(int $id, WalletRepository $walletRepository): JsonResponse
    {

        $wallet = $walletRepository->findOneBy(['id' => $id]);
        $this->permission($wallet);
        return $this->jsonResponse($wallet);
    }

}