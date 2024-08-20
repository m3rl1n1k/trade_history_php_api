<?php

namespace App\Service;

interface CalculationInterface
{
    public function calculate($wallet, $transaction, string $flag, array $options = []): void;
}