<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Twig;

use Dedi\SyliusSEOPlugin\SEO\Context\MetadataContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TitleExtension extends AbstractExtension
{
    public function __construct(
        private MetadataContextInterface $metadataContext,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dedi_sylius_seo_get_title', [$this, 'getTitle']),
        ];
    }

    public function getTitle(?string $defaultTitle): ?string
    {
        try {
            return $this->metadataContext->getMetadata()->title;
        } catch (\Exception $e) {
            return $defaultTitle;
        }
    }
}
