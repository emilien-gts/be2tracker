<?php

namespace App\Form;

use App\Entity\Bet;
use App\Enum\BetStatusEnum;
use App\Form\Base\BaseEnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'required' => true,
        ]);
        $builder->add('status', BaseEnumType::class, [
            'class' => BetStatusEnum::class,
            'required' => true,
        ]);
        $builder->add('odds', MoneyType::class, [
            'required' => true,
            'currency' => 'u',
        ]);
        $builder->add('stake', MoneyType::class, [
            'required' => true,
            'currency' => 'u',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bet::class,
            'label' => false,
        ]);
    }
}
