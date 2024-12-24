<?php

namespace App\Service\BettingSlip\Editor;

use App\Entity\Bankroll;
use App\Entity\BettingSlip;
use App\Enum\BettingSlipTypeEnum;
use App\Form\BetType;
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
        // betting slip
        $builder->add('date', DateTimeType::class, [
            'widget' => 'single_text',
            'required' => true,
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
            'data' => $bs->bets->first(),
        ]);
    }

    public function getTemplates(): array
    {
        return [
            'new' => 'betting_slip/simple/new.html.twig',
        ];
    }
}
