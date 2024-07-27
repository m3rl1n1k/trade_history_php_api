<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{

    public function __construct(protected UserRepository         $userRepository,
                                protected TransactionRepository  $transactionRepository,
                                protected EntityManagerInterface $em
    )
    {
    }

    public function newTransaction(Wallet $wallet, Transaction $transaction): void
    {

    }

//    public function editTransaction(Wallet $wallet, Transaction $transaction, float $oldAmount = 0):
//    void
//    {
//        $this->CurrentMoreOldAmount($wallet, $transaction, $oldAmount);
//        if ($transaction->getAmount() === $oldAmount && $transaction->isExpense()) {
//            $wallet->setAmount($wallet->getAmount());
//        }
//        if ($transaction->getAmount() === $oldAmount && $transaction->isIncome()) {
//            $wallet->setAmount($wallet->getAmount());
//        }
//    }
//
//    private function CurrentMoreOldAmount(Wallet $wallet, Transaction $transaction, float $oldAmount): void
//    {
//        if ($transaction->isExpense()) {
//            if ($transaction->getAmount() > $oldAmount) {
//                $difference = $transaction->getAmount() - $oldAmount;
//                $newAmount = $wallet->getAmount() - abs($difference);
//                $wallet->setAmount($newAmount);
//            } else {
//                $amount = $wallet->getAmount() + ($oldAmount - $transaction->getAmount());
//                $wallet->setAmount($amount);
//            }
//        }
//        if ($transaction->isIncome()) {
//            if ($transaction->getAmount() > $oldAmount) {
//                $wallet->setAmount(
//                    $wallet->getAmount() + abs($transaction->getAmount() - $oldAmount)
//                );
//            } else {
//                $wallet->setAmount(
//                    $wallet->getAmount() - abs($transaction->getAmount() - $oldAmount)
//                );
//            }
//        }
//    }

    public function removeTransaction(Wallet $wallet, Transaction $transaction): void
    {
        $amount = $transaction->getAmount();
        if ($transaction->isExpense($transaction)) {
            $amount = $wallet->increment($amount);
        } else {
            $amount = $wallet->decrement($amount);
        }
        $wallet->setAmount($amount);
    }

//    public function getSum(array $transactions, int $type): float
//    {
//        $sum = 0;
//        foreach ($transactions as $transaction) {
//            if ($transaction->getType() === $type) {
//                $sum += $transaction->getAmount();
//            }
//        }
//        return $sum;
//    }
//
//    public function createTransaction(Wallet $wallet, int $amount, User $user, array $options = []): void
//    {
//        $msg = 'Transfer from %s';
//        $msg = sprintf($msg, $wallet->getCardName() ?? $wallet->getNumber());
//
//        if (isset($options['rate'])) {
//            $msg = 'Transfer from %s with exchange rate: %s';
//            $msg = sprintf($msg, $wallet->getCardName() ?? $wallet->getNumber(), $options['rate']);
//        }
//        $date = new DateTimeImmutable('now');
//
//        $transaction = new Transaction();
//        $transaction->setAmount($amount)
//            ->setWallet($wallet)
//            ->setCreatedAt($date)
//            ->setTransfer()
//            ->setDescription($msg)
//            ->setUser($user);
//
//        $this->em->persist($transaction);
//    }
}