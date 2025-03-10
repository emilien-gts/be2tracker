<?php

namespace App\Form\Bankroll;

use App\Entity\Bankroll\Bankroll;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

class BankrollType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'required' => true,
        ]);
        $builder->add('capital', MoneyType::class, [
            'required' => true,
            'currency' => 'u',
            'constraints' => new Positive(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bankroll::class,
            'label' => false,
        ]);
    }
}
