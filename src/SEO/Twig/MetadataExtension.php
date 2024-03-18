<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Twig;

use Dedi\SyliusSEOPlugin\SEO\Context\MetadataContextInterface;
use Dedi\SyliusSEOPlugin\SEO\Exception\ContextNotFoundException;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MetadataExtension extends AbstractExtension
{
    public function __construct(
        private MetadataContextInterface $metadataContext,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dedi_sylius_seo_get_metadata', [$this, 'getMetadata']),
        ];
    }

    public function getMetadata(): ?Metadata
    {
        try {
            return $this->metadataContext->getMetadata();
        } catch (ContextNotFoundException $e) {
            return null;
        }
    }
}
