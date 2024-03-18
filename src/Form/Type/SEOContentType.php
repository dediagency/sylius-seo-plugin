<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

class SEOContentType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', ResourceTranslationsType::class, [
                'label' => 'dedi_sylius_seo_plugin.ui.contents',
                'entry_type' => SEOContentTranslationType::class,
                'constraints' => [new Valid()],
            ])
            ->add('robots', SEOContentRobotsType::class, [
                'label' => 'dedi_sylius_seo_plugin.ui.robots',
                'entry_type' => SEOContentRobotType::class,
                'constraints' => [new Valid()],
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

    public function getBlockPrefix(): string
    {
        return 'dedi_sylius_seo_plugin_content_seo';
    }
}
