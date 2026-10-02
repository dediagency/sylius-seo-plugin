<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class IndexableFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('indexable', ChoiceType::class, [
            'label' => false,
            'choices' => [
                'sylius.ui.yes_label' => true,
                'sylius.ui.no_label' => false,
            ],
            'required' => false,
        ]);
    }
}
