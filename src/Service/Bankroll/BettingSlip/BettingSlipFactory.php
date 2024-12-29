<?php

namespace App\Service\Bankroll\BettingSlip;

use App\Entity\Bankroll\Bet;
use App\Entity\Bankroll\BettingSlip;
use App\Enum\Bankroll\BettingSlipTypeEnum;

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
