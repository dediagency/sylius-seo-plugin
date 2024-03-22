<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Type;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SEOContentTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();

            /** @var SEOContentInterface|null $seo */
            $seo = $form->getParent()?->getParent()?->getData();

            if (null !== $seo && $seo->getType() === 'uri') {
                $form->add('uri', UrlType::class, [
                    'required' => true,
                ]);
            }
        });
    }

    public function getBlockPrefix()
    {
        return 'dedi_sylius_seo_plugin_content_seo_translation';
    }
}
