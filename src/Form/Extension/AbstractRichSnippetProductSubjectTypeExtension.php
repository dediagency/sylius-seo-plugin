<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

abstract class AbstractRichSnippetProductSubjectTypeExtension extends AbstractReferenceableTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('brand', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.brand',
                'required' => false,
            ])
            ->add('gtin8', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.gtin8',
                'required' => false,
                'constraints' => [
                    new Length([
                        'allowEmptyString' => true,
                        'min' => 8,
                        'max' => 8,
                    ]),
                ],
                'validation_groups' => ['Default', 'sylius'],
            ])
            ->add('gtin13', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.gtin13',
                'required' => false,
                'constraints' => [
                    new Length([
                        'allowEmptyString' => true,
                        'min' => 13,
                        'max' => 13,
                    ]),
                ],
                'validation_groups' => ['Default', 'sylius'],
            ])
            ->add('gtin14', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.gtin14',
                'required' => false,
                'constraints' => [
                    new Length([
                        'allowEmptyString' => true,
                        'min' => 14,
                        'max' => 14,
                    ]),
                ],
                'validation_groups' => ['Default', 'sylius'],
            ])
            ->add('mpn', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.mpn',
                'required' => false,
            ])
            ->add('isbn', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.isbn',
                'required' => false,
            ])
            ->add('sku', TextType::class, [
                'label' => 'dedi_sylius_seo_plugin.form.sku',
                'required' => false,
            ])
        ;
    }
}
