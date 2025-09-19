<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReferenceableImageExtension extends AbstractExtension
{
    public const IMAGE_PATTERN = '/^(?:\/{0,1}[\w\d]+)+.\w+$/';

    private CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dedi_sylius_seo_get_image_url', [$this, 'getImageUrl']),
        ];
    }

    public function getImageUrl(?string $imagePath, string $filterName = 'sylius_shop_product_thumbnail'): ?string
    {
        if (null === $imagePath || !preg_match(self::IMAGE_PATTERN, $imagePath)) {
            return $imagePath;
        }

        return $this->cacheManager->generateUrl($imagePath, $filterName);
    }
}
