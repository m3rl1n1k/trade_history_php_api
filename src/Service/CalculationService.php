<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Wallet;

class CalculationService implements CalculationInterface
{
    public function __construct()
    {
    }


    public function calculate($wallet, $transaction, array $options = []): void
    {
        switch ($options['flag']) {
            case "new":
                $this->newTransaction($wallet, $transaction);
                break;
            case "edit":
                $this->editTransaction($wallet, $transaction, $options['old_amount']);
                break;
            case "remove":
                $this->removeTransaction($wallet, $transaction);
                break;
        }
    }

    private function newTransaction($wallet, $transaction): void
    {
        /** @var Transaction $transaction */
        /** @var Wallet $wallet */
        if ($transaction->isIncome()) {
            $wallet->setAmountCurrent($wallet->increment($transaction->getAmountCurrent()));
        }
        if ($transaction->isExpense()) {
            $wallet->setAmountCurrent($wallet->decrement($transaction->getAmountCurrent()));
        }
    }

    private function editTransaction($wallet, $transaction, $oldAmount): void
    {
        $this->CurrentMoreOldAmount($wallet, $transaction, $oldAmount);
        if ($transaction->getAmountCurrent() === $oldAmount && $transaction->isExpense()) {
            $wallet->setAmountCurrent($wallet->getAmountCurrent());
        }
        if ($transaction->getAmountCurrent() === $oldAmount && $transaction->isIncome()) {
            $wallet->setAmountCurrent($wallet->getAmountCurrent());
        }
    }

    private function CurrentMoreOldAmount(Wallet $wallet, Transaction $transaction, float $oldAmount): void
    {
        if ($transaction->isExpense()) {
            if ($transaction->getAmountCurrent() > $oldAmount) {
                $difference = $transaction->getAmountCurrent() - $oldAmount;
                $newAmount = $wallet->getAmountCurrent() - abs($difference);
                $wallet->setAmountCurrent($newAmount);
            } else {
                $amount = $wallet->getAmountCurrent() + ($oldAmount - $transaction->getAmountCurrent());
                $wallet->setAmountCurrent($amount);
            }
        }
        if ($transaction->isIncome()) {
            if ($transaction->getAmountCurrent() > $oldAmount) {
                $wallet->setAmountCurrent(
                    $wallet->getAmountCurrent() + abs($transaction->getAmountCurrent() - $oldAmount)
                );
            } else {
                $wallet->setAmountCurrent(
                    $wallet->getAmountCurrent() - abs($transaction->getAmountCurrent() - $oldAmount)
                );
            }
        }
    }

    private function removeTransaction($wallet, $transaction): void
    {
        $amount = $transaction->getAmountCurrent();
        if ($transaction->isExpense($transaction)) {
            $amount = $wallet->increment($amount);
        } else {
            $amount = $wallet->decrement($amount);
        }
        $wallet->setAmountCurrent($amount);
    }
}