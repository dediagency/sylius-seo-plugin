<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Type\Admin;

use Dedi\SyliusSEOPlugin\Form\Type\SEOContentType as BaseSEOContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SEOContentType extends AbstractType
{
    public function __construct(
        private array $validationGroups,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => $this->validationGroups,
        ]);
    }

    public function getParent(): string
    {
        return BaseSEOContentType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'dedi_sylius_seo_plugin_content_seo_admin';
    }
}
