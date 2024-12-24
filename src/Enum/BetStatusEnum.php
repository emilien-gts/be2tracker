<?php

namespace App\Enum;

use App\Model\Contract\Labelized;

enum BetStatusEnum: string implements Labelized
{
    case PENDING = 'simple';
    case WON = 'won';
    case LOST = 'lost';
    case REFUNDED = 'refunded';
    case CASHOUT = 'cashout';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::WON => 'Gagné',
            self::LOST => 'Perdu',
            self::REFUNDED => 'Remboursé',
            self::CASHOUT => 'Cashout',
            self::CANCELLED => 'Annulé',
        };
    }
}
