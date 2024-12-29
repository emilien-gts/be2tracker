<?php

namespace App\Service;

use App\Enum\BetCombinationMethodEnum;
use App\Model\BetCombination;
use App\Utils\MathUtils;

class BetCombinationService
{
    public function combine(BetCombination $combination): BetCombination
    {
        $combination = match ($combination->method) {
            BetCombinationMethodEnum::ONE_OR_TWO => $this->combineOneOrTwo($combination),
            BetCombinationMethodEnum::ONE_REFUNDED_IF_TWO => $this->computeRefunded($combination, true),
            BetCombinationMethodEnum::TWO_REFUNDED_IF_ONE => $this->computeRefunded($combination, false),
        };

        $combination->name = $this->buildName($combination);

        return $combination;
    }

    private function combineOneOrTwo(BetCombination $combination): BetCombination
    {
        $item1 = $combination->item1;
        $item2 = $combination->item2;

        $totalStake = $combination->stake;
        $odd1 = $item1->odd;
        $odd2 = $item2->odd;

        $stake1 = MathUtils::div(MathUtils::mul($totalStake, $odd2), MathUtils::add($odd1, $odd2));
        $stake2 = MathUtils::sub($totalStake, $stake1);

        $item1->stake = MathUtils::round($stake1, 2);
        $item2->stake = MathUtils::round($stake2, 2);

        $payout1 = MathUtils::mul($stake1, $odd1);
        $payout2 = MathUtils::mul($stake2, $odd2);

        $combinationOdd = MathUtils::div(MathUtils::min($payout1, $payout2), $totalStake, 2);

        $combination->item1 = $item1;
        $combination->item2 = $item2;
        $combination->stake = MathUtils::round($totalStake, 2);
        $combination->odd = $combinationOdd;

        return $combination;
    }

    private function computeRefunded(BetCombination $combination, bool $isOneRefundedIfTwo): BetCombination
    {
        $item1 = $combination->item1;
        $item2 = $combination->item2;

        $totalStake = $combination->stake;
        $odd1 = $item1->odd;
        $odd2 = $item2->odd;

        if ($isOneRefundedIfTwo) {
            $stake2 = MathUtils::div($totalStake, MathUtils::sub($odd2, '1'));
            $stake1 = MathUtils::sub($totalStake, $stake2);
            $payout = MathUtils::mul($stake1, $odd1);
        } else {
            $stake1 = MathUtils::div($totalStake, MathUtils::sub($odd1, '1'));
            $stake2 = MathUtils::sub($totalStake, $stake1);
            $payout = MathUtils::mul($stake2, $odd2);
        }

        $item1->stake = MathUtils::round($stake1, 2);
        $item2->stake = MathUtils::round($stake2, 2);

        $combinationOdd = MathUtils::div($payout, $totalStake, 2);

        $combination->item1 = $item1;
        $combination->item2 = $item2;
        $combination->stake = MathUtils::round($totalStake, 2);
        $combination->odd = $combinationOdd;

        return $combination;
    }

    private function buildName(BetCombination $combination): string
    {
        if (null !== $combination->name) {
            return $combination->name;
        }

        $method = $combination->method;
        if (BetCombinationMethodEnum::ONE_OR_TWO === $method) {
            return sprintf('%s ou %s', $combination->item1->name, $combination->item2->name);
        }

        if (BetCombinationMethodEnum::ONE_REFUNDED_IF_TWO === $method) {
            return sprintf('%s remboursé si %s', $combination->item1->name, $combination->item2->name);
        }

        return sprintf('%s remboursé si %s', $combination->item2->name, $combination->item1->name);
    }
}
