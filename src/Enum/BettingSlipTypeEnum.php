<?php

namespace App\Enum;

use App\Model\Contract\Iconized;
use App\Model\Contract\Labelized;

enum BettingSlipTypeEnum: string implements Labelized, Iconized
{
    case SIMPLE = 'simple';
    case COMBINED = 'combined';

    public function getLabel(): string
    {
        return match ($this) {
            self::SIMPLE => 'Simple',
            self::COMBINED => 'Combined',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::SIMPLE => 'heroicons:ticket',
            self::COMBINED => 'heroicons:square-2-stack',
        };
    }
}
