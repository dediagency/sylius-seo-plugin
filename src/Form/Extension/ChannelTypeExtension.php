<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Extension;

use Dedi\SyliusSEOPlugin\Form\Type\SEOContentType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referenceableContent', SEOContentType::class, [
                'label' => 'dedi_sylius_seo_plugin.ui.seo',
                'constraints' => [new Valid()],
            ])
            ->add('googleAnalyticsCode', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.google_analytics_code',
                'attr' => [
                    'placeholder' => 'UA-XXXXX-Y',
                ],
                'required' => false,
            ])
            ->add('googleTagManagerId', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.google_tag_manager_id',
                'attr' => [
                    'placeholder' => 'GTM-XXXXXX',
                ],
                'required' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}
