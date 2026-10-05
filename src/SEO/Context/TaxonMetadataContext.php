<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Context;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Exception\ContextNotAvailableInRequestException;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use Dedi\SyliusSEOPlugin\SEO\Transformer\ReferenceableToMetadataTransformerInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class TaxonMetadataContext implements MetadataContextInterface
{
    /** @param TaxonRepositoryInterface<TaxonInterface> $repository */
    public function __construct(
        private readonly FilterInterface $filter,
        private readonly TaxonRepositoryInterface $repository,
        private readonly LocaleContextInterface $localeContext,
        private readonly RequestStack $requestStack,
        private readonly ReferenceableToMetadataTransformerInterface $transformer,
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

        Assert::isInstanceOf($taxon, ReferenceableInterface::class);

        return $this->transformer->transform($taxon);
    }
}
