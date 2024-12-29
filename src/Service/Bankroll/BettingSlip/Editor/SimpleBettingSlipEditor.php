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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

class SimpleBettingSlipEditor extends AbstractBettingSlipEditor
{
    public function supportBettingSlip(BettingSlip $bs): bool
    {
        return BettingSlipTypeEnum::SIMPLE === $bs->type;
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

        $builder->add('bankroll', EntityType::class, [
            'class' => Bankroll::class,
            'required' => true,
        ]);

        // bet
        $bs = $builder->getData();
        if (!$bs instanceof BettingSlip) {
            throw new \InvalidArgumentException('Invalid betting slip');
        }

        $builder->add('bet', BetType::class, [
            'data' => $bs->getBet(),
        ]);
    }

    public function computeOdds(BettingSlip $bs): string
    {
        if (null === $bs->getBet()) {
            throw new \InvalidArgumentException('Invalid betting slip');
        }

        return $bs->getBet()->odds;
    }

    public function computeStake(BettingSlip $bs): string
    {
        if (null === $bs->getBet()?->stake) {
            throw new \InvalidArgumentException('Invalid betting slip');
        }

        return $bs->getBet()->stake;
    }

    public function computeStatus(BettingSlip $bs): BetStatusEnum
    {
        if (null === $bs->getBet()?->status) {
            throw new \InvalidArgumentException('Invalid betting slip');
        }

        return $bs->getBet()->status;
    }

    public function computeOutcome(BettingSlip $bs): string
    {
        if (null === $bs->getBet()?->outcome) {
            throw new \InvalidArgumentException('Invalid betting slip');
        }

        return $bs->getBet()->outcome;
    }

    public function computeProfit(BettingSlip $bs): string
    {
        if (null === $bs->getBet()?->profit) {
            throw new \InvalidArgumentException('Invalid betting slip');
        }

        return $bs->getBet()->profit;
    }

    public function getTemplates(): array
    {
        return [
            'new' => 'betting_slip/simple/new.html.twig',
            'edit' => 'betting_slip/simple/edit.html.twig',
        ];
    }

    public function save(BettingSlip $bs): void
    {
        /** @var Bet $bet */
        $bet = $bs->getBet();
        if (null === $bet->stake) {
            throw new \InvalidArgumentException('Invalid stake');
        }

        $bet->outcome = match ($bet->status) {
            BetStatusEnum::PENDING => '0.00',
            BetStatusEnum::WON => MathUtils::mul($bet->stake, $bet->odds),
            BetStatusEnum::LOST => MathUtils::sub('0.00', $bet->stake),
            BetStatusEnum::REFUNDED, BetStatusEnum::CANCELLED => $bet->stake,
        };

        $bet->profit = match ($bet->status) {
            BetStatusEnum::WON => MathUtils::sub(MathUtils::mul($bet->stake, $bet->odds), $bet->stake),
            BetStatusEnum::LOST => MathUtils::sub('0.00', $bet->stake),
            BetStatusEnum::PENDING, BetStatusEnum::REFUNDED, BetStatusEnum::CANCELLED => '0.00',
        };

        parent::save($bs);
    }
}
