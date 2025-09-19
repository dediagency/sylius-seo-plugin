<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Extension;

use Dedi\SyliusSEOPlugin\Form\Type\SEOContentType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

class ProductTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referenceableContent', SEOContentType::class, [
                'label' => 'dedi_sylius_seo_plugin.ui.seo',
                'constraints' => [new Valid()],
            ])
            ->add('SEOBrand', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.brand',
                'required' => false,
            ])
            ->add('SEOGtin8', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.gtin8',
                'required' => false,
                'constraints' => [
                    new AtLeastOneOf([
                        'constraints' => [
                            new Length([
                                'min' => 8,
                                'max' => 8,
                            ]),
                            new Blank(),
                        ],
                    ]),
                ],
                'validation_groups' => ['Default', 'sylius'],
            ])
            ->add('SEOGtin13', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.gtin13',
                'required' => false,
                'constraints' => [
                    new AtLeastOneOf([
                        'constraints' => [
                            new Length([
                                'min' => 13,
                                'max' => 13,
                            ]),
                            new Blank(),
                        ],
                    ]),
                ],
                'validation_groups' => ['Default', 'sylius'],
            ])
            ->add('SEOGtin14', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.gtin14',
                'required' => false,
                'constraints' => [
                    new AtLeastOneOf([
                        'constraints' => [
                            new Length([
                                'min' => 14,
                                'max' => 14,
                            ]),
                            new Blank(),
                        ],
                    ]),
                ],
                'validation_groups' => ['Default', 'sylius'],
            ])
            ->add('SEOMpn', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.mpn',
                'required' => false,
            ])
            ->add('SEOIsbn', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.isbn',
                'required' => false,
            ])
            ->add('SEOSku', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.sku',
                'required' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}
