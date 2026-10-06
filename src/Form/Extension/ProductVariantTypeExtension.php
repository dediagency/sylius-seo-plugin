<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Extension;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductVariantSubjectInterface;
use Sylius\Bundle\AdminBundle\Form\Type\ProductVariantType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Length;

class ProductVariantTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            if (!$event->getData() instanceof RichSnippetProductVariantSubjectInterface) {
                return;
            }

            $event->getForm()
                ->add('SEOGtin8', TextType::class, $this->getGtinOptions('gtin8', 8))
                ->add('SEOGtin13', TextType::class, $this->getGtinOptions('gtin13', 13))
                ->add('SEOGtin14', TextType::class, $this->getGtinOptions('gtin14', 14))
                ->add('SEOMpn', TextType::class, [
                    'label' => 'dedi_sylius_seo_plugin.form.mpn',
                    'required' => false,
                ])
                ->add('SEOSku', TextType::class, [
                    'label' => 'dedi_sylius_seo_plugin.form.sku',
                    'required' => false,
                ])
            ;
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductVariantType::class];
    }

    /**
     * @param positive-int $length
     */
    private function getGtinOptions(string $label, int $length): array
    {
        return [
            'label' => 'dedi_sylius_seo_plugin.form.' . $label,
            'required' => false,
            'constraints' => [
                new AtLeastOneOf(constraints: [
                    new Length(min: $length, max: $length),
                    new Blank(),
                ]),
            ],
            'validation_groups' => ['Default', 'sylius'],
        ];
    }
}
