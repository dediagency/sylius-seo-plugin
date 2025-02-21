<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Twig\Component;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\Factory\SEOContentFactoryInterface;
use Sylius\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
class SeoContentFormComponent
{
    use LiveCollectionTrait;
    use TemplatePropTrait;

    /** @use ResourceFormComponentTrait<SEOContentInterface> */
    use ResourceFormComponentTrait;

    #[LiveProp(writable: false)]
    public string $type = 'product';

    /** @param RepositoryInterface<SEOContentInterface> $seoContentRepository */
    public function __construct(
        RepositoryInterface $seoContentRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly SEOContentFactoryInterface $seoContentFactory,
    ) {
        $this->initialize($seoContentRepository, $formFactory, $resourceClass, $formClass);
    }

    /** @return SEOContentInterface */
    protected function createResource(): ResourceInterface
    {
        return $this->seoContentFactory->createTyped($this->type);
    }
}
