<?php

namespace App\Tests\Factory;

use App\Entity\BettingSlip;
use App\Enum\BettingSlipTypeEnum;
use App\Service\BettingSlip\BettingSlipEditorService;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<BettingSlip>
 */
final class BettingSlipFactory extends PersistentProxyObjectFactory
{
    public function __construct(private readonly BettingSlipEditorService $bsEditorService)
    {
    }

    public static function class(): string
    {
        return BettingSlip::class;
    }

    protected function defaults(): array
    {
        return [
            'bankroll' => BankrollFactory::new(),
            'date' => self::faker()->dateTimeBetween('-1 year'),
            'name' => self::faker()->text(25),
            'type' => self::faker()->randomElement(BettingSlipTypeEnum::cases()),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterPersist(function (BettingSlip $bs): void {
            match ($bs->type) {
                BettingSlipTypeEnum::SIMPLE => $this->initializeSimple($bs),
                BettingSlipTypeEnum::COMBINED => $this->initializeCombined($bs),
                default => throw new \InvalidArgumentException('Invalid betting slip type'),
            };

            $editor = $this->bsEditorService->getEditor($bs);

            $bs->odds = $editor->computeOdds($bs);
            $bs->stake = $editor->computeStake($bs);
            $bs->status = $editor->computeStatus($bs);
            $bs->outcome = $editor->computeOutcome($bs);
        });
    }

    private function initializeSimple(BettingSlip $bs): void
    {
        BetFactory::createOne(['bettingSlip' => $bs]);
    }

    private function initializeCombined(BettingSlip $bs): void
    {
        $bs->stake = (string) self::faker()->randomFloat(2, 1, 100);
        BetFactory::createMany(3, ['bettingSlip' => $bs, 'stake' => $bs->stake]);
    }
}
