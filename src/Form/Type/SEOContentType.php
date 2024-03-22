<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Form\Type;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAutocompleteChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class SEOContentType extends AbstractResourceType
{
    public function __construct(
        private LocaleContextInterface $localeContext,
        private EventSubscriberInterface $eventSubscriber,
        string $dataClass,
        array $validationGroups,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

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
            ->add('product', ProductAutocompleteChoiceType::class, [
                'required' => true,
            ])
            ->add('taxon', TaxonAutocompleteChoiceType::class, [
                'required' => true,
            ])
        ;

        $builder->addEventSubscriber($this->eventSubscriber);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $parent = $form->getParent();
        if (null !== $parent && $parent->getData() instanceof ReferenceableInterface) {
            $view->vars['resource'] = $parent->getData();
        } else {
            /** @var SEOContentInterface $data */
            $data = $form->getData();
            if (null === $data->getId()) {
                $data->setCurrentLocale($this->localeContext->getLocaleCode());
            }

            $view->vars['resource'] = $data;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('type', null);
    }

    public function getBlockPrefix(): string
    {
        return 'dedi_sylius_seo_plugin_content_seo';
    }
}
