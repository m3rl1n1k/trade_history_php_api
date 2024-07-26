<?php

namespace App\Repository;

use App\Entity\Wallet;
use App\Service\CurrencyConverter\ExchangeService;
use App\Service\SettingService;
use App\Trait\EntityRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Wallet>
 */
class WalletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry                  $registry,
                                protected Security               $security,
                                private readonly SettingService  $settingService,
                                private readonly ExchangeService $exchangeService)
    {
        parent::__construct($registry, Wallet::class);
    }

    use EntityRepositoryTrait;

    public function getTotalAmountOfUser()
    {
        $user = $this->security->getUser();
        $wallets = $this->findBy(['user' => $user]);

        $sum = 0;

        foreach ($wallets as $wallet) {
            if ($wallet->getCurrency() === $this->settingService->currencySelected())
                $sum += $wallet->getAmount();
            else
                $sum += $wallet->getAmount() * $this->exchangeService->currencyExchange("{$wallet->getCurrency()}_{$this->settingService->currencySelected()}");
        }
        return $sum;
    }
}
