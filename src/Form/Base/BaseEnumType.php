<?php

namespace App\Form\Base;

use App\Model\Contract\Labelized;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('choice_label', static function (\UnitEnum $choice) {
                return $choice instanceof Labelized ? $choice->getLabel() : $choice->name;
            });
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
