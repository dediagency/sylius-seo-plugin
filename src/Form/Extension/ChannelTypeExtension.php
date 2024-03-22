<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Extension;

use Dedi\SyliusSEOPlugin\Form\Type\SEOContentType;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Enum\MetadataTypeEnum;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;

class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            if ($event->getData() instanceof ReferenceableInterface) {
                $event->getForm()
                    ->add('referenceableContent', SEOContentType::class, [
                        'label' => 'dedi_sylius_seo_plugin.ui.seo',
                        'constraints' => [new Valid()],
                        'type' => MetadataTypeEnum::CHANNEL,
                    ])
                ;
            }
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}
