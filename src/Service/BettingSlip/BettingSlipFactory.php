<?php

namespace App\Service\BettingSlip;

use App\Entity\Bet;
use App\Entity\BettingSlip;
use App\Enum\BettingSlipTypeEnum;

class BettingSlipFactory
{
    public function create(BettingSlipTypeEnum $type): BettingSlip
    {
        return match ($type) {
            BettingSlipTypeEnum::SIMPLE => $this->createSimple(),
            default => throw new \InvalidArgumentException('Not implemented yet'),
        };
    }

    private function createSimple(): BettingSlip
    {
        $bs = new BettingSlip();
        $bs->type = BettingSlipTypeEnum::SIMPLE;

        $bet = new Bet();
        $bs->setBet($bet);

        return $bs;
    }
}
