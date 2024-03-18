<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Twig;

use Liip\ImagineBundle\Templating\FilterExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReferenceableImageExtension extends AbstractExtension
{
    public const IMAGE_PATTERN = '/^(?:\/{0,1}[\w\d]+)+.\w+$/';

    private FilterExtension $filterExtension;

    public function __construct(FilterExtension $filterExtension)
    {
        $this->filterExtension = $filterExtension;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dedi_sylius_seo_get_image_url', [$this, 'getImageUrl']),
        ];
    }

    public function getImageUrl(?string $imagePath, string $filterName = 'sylius_shop_product_thumbnail'): ?string
    {
        if (null === $imagePath || false === (bool) preg_match(self::IMAGE_PATTERN, $imagePath)) {
            return $imagePath;
        }

        return $this->filterExtension->filter($imagePath, $filterName);
    }
}
