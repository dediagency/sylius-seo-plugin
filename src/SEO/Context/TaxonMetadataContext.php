<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Context;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableAwareInterface;
use Dedi\SyliusSEOPlugin\SEO\Enum\MetadataOriginEnum;
use Dedi\SyliusSEOPlugin\SEO\Exception\ContextNotAvailableInRequestException;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use Dedi\SyliusSEOPlugin\SEO\Transformer\SEOContentToMetadataTransformerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class TaxonMetadataContext implements MetadataContextInterface
{
    public function __construct(
        private FilterInterface $filter,
        private TaxonRepositoryInterface $repository,
        private LocaleContextInterface $localeContext,
        private RequestStack $requestStack,
        private SEOContentToMetadataTransformerInterface $transformer,
    ) {
    }

    public function getMetadata(): Metadata
    {
        $request = $this->requestStack->getCurrentRequest();

        Assert::notNull($request);

        if (!$this->filter->isSatisfiedBy($request)) {
            throw new ContextNotAvailableInRequestException();
        }

        $slug = $request->attributes->get('slug');

        Assert::string($slug);

        $taxon = $this->repository->findOneBySlug(
            $slug,
            $this->localeContext->getLocaleCode(),
        );

        Assert::isInstanceOf($taxon, ReferenceableAwareInterface::class);

        return $this->transformer->transform($taxon->getReferenceableContent(), MetadataOriginEnum::TAXON);
    }
}
