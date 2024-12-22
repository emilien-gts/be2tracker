<?php

namespace App\Model\Bankroll;

use App\Utils\MathUtils;

class BankrollMovement
{
    public function __construct(
        public \DateTime $date,
        public string $movement,
        public string $currentBalance,
    ) {
        $this->currentBalance = MathUtils::round($this->currentBalance, 2);
    }
}
