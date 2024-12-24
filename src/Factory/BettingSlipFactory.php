<?php

namespace App\Factory;

use App\Entity\BettingSlip;
use App\Enum\BettingSlipTypeEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<BettingSlip>
 */
final class BettingSlipFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return BettingSlip::class;
    }

    protected function defaults(): array
    {
        return [
            'bankroll' => BankrollFactory::new(),
            'date' => self::faker()->dateTime(),
            'name' => self::faker()->text(255),
            'type' => self::faker()->randomElement(BettingSlipTypeEnum::cases()),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterPersist(function (BettingSlip $bs): void {
            if (BettingSlipTypeEnum::SIMPLE === $bs->type) {
                BetFactory::createMany(1, ['bettingSlip' => $bs]);
            }

            if (BettingSlipTypeEnum::COMBINED === $bs->type) {
                BetFactory::createMany(3, ['bettingSlip' => $bs]);
            }
        });
    }
}
