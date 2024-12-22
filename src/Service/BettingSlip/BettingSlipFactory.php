<?php

namespace App\Service\BettingSlip;

use App\Entity\Bet;
use App\Entity\BettingSlip;
use App\Enum\BettingSlipTypeEnum;

class BettingSlipFactory
{
    public function create(BettingSlipTypeEnum $type): BettingSlip
    {
        $bs = new BettingSlip();
        $bs->type = $type;

        match ($type) {
            BettingSlipTypeEnum::SIMPLE => $bs->setBet(new Bet()),
            BettingSlipTypeEnum::COMBINED => $bs->addBet(new Bet()),
        };

        return $bs;
    }
}
