<?php

namespace App\Controller;

use App\Repository\CurrencyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/currency')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class CurrencyController extends BaseController
{
    #[Route('/', name: 'currency_index', methods: ['GET'])]
    public function index(CurrencyRepository $currencyRepository): JsonResponse
    {
        return $this->jsonResponse([
            'currencies' => $currencyRepository->findAll(),
        ]);
    }
}