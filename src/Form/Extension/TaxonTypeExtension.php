<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Extension;

use Dedi\SyliusSEOPlugin\Form\Type\SEOContentType;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableAwareInterface;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;

class TaxonTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            if ($event->getData() instanceof ReferenceableAwareInterface) {
                $form
                    ->add('referenceableContent', SEOContentType::class, [
                        'label' => 'dedi_sylius_seo_plugin.ui.seo',
                        'constraints' => [new Valid()],
                    ])
                ;
            }
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [TaxonType::class];
    }
}
