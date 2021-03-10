<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SEOContentTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('notIndexable', CheckboxType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.not_indexable',
            ])

            ->add('metadataTitle', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.metadata_title',
                'required' => false,
            ])
            ->add('metadataDescription', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.metadata_description',
                'required' => false,
            ])

            ->add('openGraphMetadataTitle', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.og_metadata_title',
                'required' => false,
            ])
            ->add('openGraphMetadataDescription', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.og_metadata_description',
                'required' => false,
            ])
            ->add('openGraphMetadataUrl', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.og_metadata_url',
                'required' => false,
            ])
            ->add('openGraphMetadataImage', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.og_metadata_image',
                'required' => false,
            ])
            ->add('openGraphMetadataType', ChoiceType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.og_metadata_type',
                'choices' => [
                    'website' => 'website',
                    'page' => 'article',
                ],
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'dedi_sylius_seo_plugin_content_seo_translation';
    }
}
