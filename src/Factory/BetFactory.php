<?php

namespace App\Factory;

use App\Entity\Bet;
use App\Enum\BetStatusEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Bet>
 */
final class BetFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Bet::class;
    }

    protected function defaults(): array
    {
        return [
            'bettingSlip' => BettingSlipFactory::new(),
            'name' => self::faker()->text(255),
            'odds' => self::faker()->randomFloat(2, 0, 3),
            'outcome' => self::faker()->randomFloat(2, 0, 500),
            'stake' => self::faker()->randomFloat(2, 0, 7),
            'status' => self::faker()->randomElement(BetStatusEnum::cases()),
        ];
    }
}
