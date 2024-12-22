<?php

namespace App\Enum;

use App\Model\Contract\Colorized;
use App\Model\Contract\Iconized;
use App\Model\Contract\Labelized;

enum BetStatusEnum: string implements Labelized, Colorized, Iconized
{
    case PENDING = 'pending';
    case WON = 'won';
    case LOST = 'lost';
    case REFUNDED = 'refunded';
    // case CASHOUT = 'cashout';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::WON => 'Gagné',
            self::LOST => 'Perdu',
            self::REFUNDED => 'Remboursé',
            // self::CASHOUT => 'Cashout',
            self::CANCELLED => 'Annulé',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'orange',
            self::WON => 'green',
            self::LOST => 'red',
            self::REFUNDED => 'blue',
            // self::CASHOUT => 'purple',
            self::CANCELLED => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicons:clock',
            self::WON => 'heroicons:check-circle',
            self::LOST => 'heroicons:x-circle',
            self::REFUNDED => 'heroicons:arrow-down-on-square',
            // self::CASHOUT => 'heroicons:cash',
            self::CANCELLED => 'heroicons:x-mark',
        };
    }
}
