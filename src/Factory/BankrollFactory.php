<?php

namespace App\Factory;

use App\Entity\Bankroll;
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
            'name' => self::faker()->unique()->text(50),
            'startingBankroll' => self::faker()->randomFloat(2, 0, 1000),
        ];
    }
}
