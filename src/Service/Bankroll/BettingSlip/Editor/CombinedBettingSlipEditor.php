<?php

namespace App\Service\Bankroll\BettingSlip\Editor;

use App\Entity\Bankroll\Bankroll;
use App\Entity\Bankroll\Bet;
use App\Entity\Bankroll\BettingSlip;
use App\Enum\Bankroll\BetStatusEnum;
use App\Enum\Bankroll\BettingSlipTypeEnum;
use App\Form\Bankroll\BetType;
use App\Utils\MathUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CombinedBettingSlipEditor extends AbstractBettingSlipEditor
{
    public function supportBettingSlip(BettingSlip $bs): bool
    {
        return BettingSlipTypeEnum::COMBINED === $bs->type;
    }

    protected function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        /** @var BettingSlip $bs */
        $bs = $builder->getData();

        // betting slip
        $builder->add('date', DateTimeType::class, [
            'widget' => 'single_text',
            'required' => true,
            'data' => $bs->date ?? new \DateTime(),
        ]);

        $builder->add('name', TextType::class, [
            'required' => false,
        ]);

        $builder->add('stake', MoneyType::class, [
            'required' => true,
            'currency' => 'u',
        ]);

        $builder->add('bankroll', EntityType::class, [
            'class' => Bankroll::class,
            'required' => true,
        ]);

        // bet
        $bs = $builder->getData();
        if (!$bs instanceof BettingSlip) {
            throw new \InvalidArgumentException('Invalid betting slip');
        }

        $builder->add('bets', CollectionType::class, [
            'entry_type' => BetType::class,
            'entry_options' => [
                'block_prefix' => 'combined_bet',
                'with_stake' => false,
                'label' => false,
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'attr' => [
                'data-controller' => 'form-collection',
            ],
            'label' => false,
        ]);
    }

    public function computeOdds(BettingSlip $bs): string
    {
        return $bs->getBets()
            ->filter(fn (Bet $bet) => !$bet->isCancelled() || !$bet->isRefunded())
            ->reduce(fn (string $odds, Bet $bet) => MathUtils::mul($odds, $bet->odds), '1.00');
    }

    public function computeStake(BettingSlip $bs): string
    {
        return $bs->stake;
    }

    public function computeStatus(BettingSlip $bs): BetStatusEnum
    {
        // if at least one bet is pending, the betting slip is pending
        if ($bs->hasBet(BetStatusEnum::PENDING)) {
            return BetStatusEnum::PENDING;
        }

        // else if at least one bet is lost, the betting slip is lost
        if ($bs->hasBet(BetStatusEnum::LOST)) {
            return BetStatusEnum::LOST;
        }

        // else if betting slip has only refunded bets, the betting slip is refunded
        if ($bs->hasOnlyBet(BetStatusEnum::REFUNDED)) {
            return BetStatusEnum::REFUNDED;
        }

        // else if betting slip has only cancelled bets, the betting slip is cancelled
        if ($bs->hasOnlyBet(BetStatusEnum::CANCELLED)) {
            return BetStatusEnum::CANCELLED;
        }

        // else the betting slip is won
        return BetStatusEnum::WON;
    }

    public function computeOutcome(BettingSlip $bs): string
    {
        return match ($bs->status) {
            BetStatusEnum::PENDING => '0.00',
            BetStatusEnum::WON => MathUtils::mul($bs->stake, $bs->odds),
            BetStatusEnum::LOST => MathUtils::sub('0.00', $bs->stake),
            BetStatusEnum::REFUNDED, BetStatusEnum::CANCELLED => $bs->stake,
        };
    }

    public function computeProfit(BettingSlip $bs): string
    {
        return match ($bs->status) {
            BetStatusEnum::WON => MathUtils::sub(MathUtils::mul($bs->stake, $bs->odds), $bs->stake),
            BetStatusEnum::LOST => MathUtils::sub('0.00', $bs->stake),
            BetStatusEnum::PENDING, BetStatusEnum::REFUNDED, BetStatusEnum::CANCELLED => '0.00',
        };
    }

    public function getTemplates(): array
    {
        return [
            'new' => 'betting_slip/combined/new.html.twig',
            'edit' => 'betting_slip/combined/edit.html.twig',
        ];
    }
}
