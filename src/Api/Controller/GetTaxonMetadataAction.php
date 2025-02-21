<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Api\Controller;

use Dedi\SyliusSEOPlugin\Repository\SEOContentRepositoryInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Builder\MetadataDirectorInterface;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use Dedi\SyliusSEOPlugin\SEO\Transformer\ReferenceableToMetadataTransformerInterface;
use Dedi\SyliusSEOPlugin\SEO\Transformer\SEOContentToMetadataTransformerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class GetTaxonMetadataAction
{
    /** @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository */
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly TaxonRepositoryInterface $taxonRepository,
        private readonly ReferenceableToMetadataTransformerInterface $transformer,
        private readonly SEOContentToMetadataTransformerInterface $SEOContentToMetadataTransformer,
        private readonly MetadataDirectorInterface $metadataDirector,
        private readonly RequestStack $requestStack,
        private readonly LocaleContextInterface $localeContext,
        private readonly SEOContentRepositoryInterface $SEOContentRepository,
    ) {
    }

    public function __invoke(int $id): Metadata
    {
        $taxon = $this->taxonRepository->find($id);

        Assert::isInstanceOf($taxon, ReferenceableInterface::class);

        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ReferenceableInterface::class);

        $request = $this->requestStack->getCurrentRequest();
        Assert::notNull($request);

        /** @var string|null $uri */
        $uri = $request->query->get('uri');
        $uriSEO = null;
        if (null !== $uri) {
            $uriSEO = $this->SEOContentRepository->findOneByUri(
                $uri,
                $this->localeContext->getLocaleCode(),
            );
        }

        $metadata = [];
        if (null !== $uriSEO) {
            $metadata[] = $this->SEOContentToMetadataTransformer->transform($uriSEO);
        }
        $metadata[] = $this->transformer->transform($taxon);
        $metadata[] = $this->transformer->transform($channel);

        $metadata = $this->metadataDirector->buildHierarchical($metadata);

        return $metadata;
    }
}
