<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Context;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\Repository\SEOContentRepositoryInterface;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use Dedi\SyliusSEOPlugin\SEO\Transformer\SEOContentToMetadataTransformerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class UriMetadataContext implements MetadataContextInterface
{
    public function __construct(
        private SEOContentRepositoryInterface $repository,
        private LocaleContextInterface $localeContext,
        private RequestStack $requestStack,
        private SEOContentToMetadataTransformerInterface $transformer,
    ) {
    }

    public function getMetadata(): Metadata
    {
        $request = $this->requestStack->getCurrentRequest();

        Assert::notNull($request);

        $seo = $this->repository->findOneByUri(
            $request->getUri(),
            $this->localeContext->getLocaleCode(),
        );

        Assert::isInstanceOf($seo, SEOContentInterface::class);

        return $this->transformer->transform($seo);
    }
}
