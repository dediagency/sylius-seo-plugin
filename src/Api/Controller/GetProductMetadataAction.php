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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class GetProductMetadataAction
{
    /** @param ProductRepositoryInterface<ProductInterface> $productRepository */
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly ProductRepositoryInterface $productRepository,
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
        /** @var ProductInterface $product */
        $product = $this->productRepository->find($id);

        Assert::isInstanceOf($product, ReferenceableInterface::class);

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
        $metadata[] = $this->transformer->transform($product);
        $metadata[] = $this->transformer->transform($channel);

        $metadata = $this->metadataDirector->buildHierarchical($metadata);

        return $metadata;
    }
}
