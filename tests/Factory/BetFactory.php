<?php

namespace App\Tests\Factory;

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
            'name' => self::faker()->text(25),
            'odds' => self::faker()->randomFloat(1, 0.1, 3),
            'stake' => self::faker()->randomFloat(2, 0.1, 3),
            'status' => $this->randomStatus(),
        ];
    }

    private function randomStatus(): BetStatusEnum
    {
        $int = self::faker()->numberBetween(0, 100);
        if ($int < 5) {
            return BetStatusEnum::REFUNDED;
        }

        if ($int < 10) {
            return BetStatusEnum::CANCELLED;
        }

        if ($int < 25) {
            return BetStatusEnum::PENDING;
        }

        if ($int < 50) {
            return BetStatusEnum::WON;
        }

        return BetStatusEnum::LOST;
    }
}
