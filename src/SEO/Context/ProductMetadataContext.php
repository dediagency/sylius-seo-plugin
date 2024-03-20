<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Context;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Enum\MetadataOriginEnum;
use Dedi\SyliusSEOPlugin\SEO\Exception\ContextNotAvailableInRequestException;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use Dedi\SyliusSEOPlugin\SEO\Transformer\ReferenceableToMetadataTransformerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class ProductMetadataContext implements MetadataContextInterface
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private FilterInterface $filter,
        private ProductRepositoryInterface $repository,
        private LocaleContextInterface $localeContext,
        private RequestStack $requestStack,
        private ReferenceableToMetadataTransformerInterface $transformer,
    ) {
    }

    public function getMetadata(): Metadata
    {
        $request = $this->requestStack->getCurrentRequest();

        Assert::notNull($request);

        if (!$this->filter->isSatisfiedBy($request)) {
            throw new ContextNotAvailableInRequestException();
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $slug = $request->attributes->get('slug');

        Assert::string($slug);

        $product = $this->repository->findOneByChannelAndSlug(
            $channel,
            $this->localeContext->getLocaleCode(),
            $slug,
        );

        Assert::isInstanceOf($product, ReferenceableInterface::class);

        return $this->transformer->transform($product, MetadataOriginEnum::PRODUCT);
    }
}
