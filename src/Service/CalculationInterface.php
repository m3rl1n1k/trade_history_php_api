<?php

namespace App\Service;

interface CalculationInterface
{
    public function calculate($wallet, $transaction, array $options = []): void;
}