<?php

namespace App\Tests\Factory;

use App\Entity\Bankroll\Bankroll;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Bankroll>
 */
final class BankrollFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Bankroll::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->text(50),
            'capital' => self::faker()->randomFloat(2, 100, 1000),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterPersist(function (Bankroll $bankroll): void {
            BettingSlipFactory::createMany(50, ['bankroll' => $bankroll]);
        });
    }
}
