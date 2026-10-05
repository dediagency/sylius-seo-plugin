<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Extension;

use Dedi\SyliusSEOPlugin\Form\Type\SEOContentType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

abstract class AbstractReferenceableTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referenceableContent', SEOContentType::class, [
                'label' => 'dedi_sylius_seo_plugin.ui.seo',
                'constraints' => [new Valid()],
            ])
        ;
    }
}
