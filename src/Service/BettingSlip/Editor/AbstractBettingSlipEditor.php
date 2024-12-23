<?php

namespace App\Service\BettingSlip\Editor;

use App\Entity\BettingSlip;
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
        $this->em->persist($bs);
        $this->em->flush();
    }
}
