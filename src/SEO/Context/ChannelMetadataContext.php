<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Context;

use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Enum\MetadataOriginEnum;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use Dedi\SyliusSEOPlugin\SEO\Transformer\ReferenceableToMetadataTransformerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Webmozart\Assert\Assert;

class ChannelMetadataContext implements MetadataContextInterface
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private ReferenceableToMetadataTransformerInterface $transformer,
    ) {
    }

    public function getMetadata(): Metadata
    {
        $channel = $this->channelContext->getChannel();

        Assert::isInstanceOf($channel, ReferenceableInterface::class);

        return $this->transformer->transform($channel, MetadataOriginEnum::CHANNEL);
    }
}
