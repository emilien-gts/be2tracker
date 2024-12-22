<?php

namespace App\Service\BettingSlip\Editor;

use App\Entity\BettingSlip;
use App\Enum\BetStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AutoconfigureTag('app.betting_slip_editor')]
abstract class AbstractBettingSlipEditor
{
    #[Required]
    public FormFactoryInterface $formFactory;

    #[Required]
    public EntityManagerInterface $em;

    abstract public function supportBettingSlip(BettingSlip $bs): bool;

    abstract protected function buildForm(FormBuilderInterface $builder, array $options = []): void;

    abstract public function computeOdds(BettingSlip $bs): string;

    abstract public function computeStake(BettingSlip $bs): string;

    abstract public function computeStatus(BettingSlip $bs): BetStatusEnum;

    abstract public function computeOutcome(BettingSlip $bs): string;

    abstract public function computeProfit(BettingSlip $bs): string;

    final public function createForm(BettingSlip $bs, array $options = []): FormInterface
    {
        $_options = \array_merge([
            'label' => false,
        ], $options);

        $builder = $this->formFactory->createBuilder(FormType::class, $bs, $_options);
        $this->buildForm($builder, $options);

        return $builder->getForm();
    }

    public function getTemplates(): array
    {
        return [];
    }

    public function save(BettingSlip $bs): void
    {
        $bs->odds = $this->computeOdds($bs);
        $bs->stake = $this->computeStake($bs);
        $bs->outcome = $this->computeOutcome($bs);
        $bs->status = $this->computeStatus($bs);
        $bs->profit = $this->computeProfit($bs);

        $this->em->persist($bs);
        $this->em->flush();
    }

    public function delete(BettingSlip $bs): void
    {
        $this->em->remove($bs);
        $this->em->flush();
    }
}
