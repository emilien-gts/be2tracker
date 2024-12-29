<?php

namespace App\Form;

use App\Enum\BetCombinationMethodEnum;
use App\Form\Base\BaseEnumType;
use App\Model\BetCombination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BetCombinationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('method', BaseEnumType::class, [
            'class' => BetCombinationMethodEnum::class,
            'required' => true,
        ]);
        $builder->add('stake', MoneyType::class, [
            'required' => true,
            'currency' => 'u',
        ]);
        $builder->add('item1', BetCombinationItemType::class, [
            'label' => 'Bet #1',
        ]);
        $builder->add('item2', BetCombinationItemType::class, [
            'label' => 'Bet #2',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BetCombination::class,
        ]);
    }
}
