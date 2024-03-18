<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class SEOContentRobotType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('notIndexable', CheckboxType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.not_indexable',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'dedi_sylius_seo_plugin_content_index';
    }
}
