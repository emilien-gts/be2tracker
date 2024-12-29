<?php

namespace App\Enum;

use App\Model\Contract\Labelized;

enum BetCombinationMethodEnum: string implements Labelized
{
    case ONE_OR_TWO = 'one_or_two';
    case ONE_REFUNDED_IF_TWO = 'one_refunded_if_two';
    case TWO_REFUNDED_IF_ONE = 'two_refunded_if_one';

    public function getLabel(): string
    {
        return match ($this) {
            self::ONE_OR_TWO => '#1 ou #2',
            self::ONE_REFUNDED_IF_TWO => '#1 remboursé si #2',
            self::TWO_REFUNDED_IF_ONE => '#2 remboursé si #1',
        };
    }
}
