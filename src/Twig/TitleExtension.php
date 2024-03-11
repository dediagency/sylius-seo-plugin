<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Twig;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TitleExtension extends AbstractExtension
{
    public function __construct(
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dedi_sylius_seo_get_title', [$this, 'getTitle']),
        ];
    }

    public function getTitle(?ReferenceableInterface $referenceable): string
    {
        if (null !== $referenceable && null !== $referenceable->getMetadataTitle()) {
            return $referenceable->getMetadataTitle();
        }

        $channel = $this->channelContext->getChannel();

        if ($channel instanceof ReferenceableInterface && null !== $channel->getMetadataTitle()) {
            return $channel->getMetadataTitle();
        }

        return 'Sylius';
    }
}
