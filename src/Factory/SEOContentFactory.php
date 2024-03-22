<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

class SEOContentFactory implements SEOContentFactoryInterface
{
    public function __construct(private LocaleProviderInterface $localeProvider)
    {
    }

    public function createNew(): SEOContentInterface
    {
        return new SEOContent();
    }

    public function createTyped(string $type): SEOContentInterface
    {
        $seoContent = $this->createNew();
        $seoContent->setType($type);
        $seoContent->setCurrentLocale($this->localeProvider->getDefaultLocaleCode());
        $seoContent->setFallbackLocale($this->localeProvider->getDefaultLocaleCode());

        return $seoContent;
    }
}
