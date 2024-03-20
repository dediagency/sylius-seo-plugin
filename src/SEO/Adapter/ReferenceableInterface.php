<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Adapter;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;

interface ReferenceableInterface extends MetadataAwareInterface
{
    public function getReferenceableContent(): SEOContentInterface;

    public function setReferenceableLocale(string $locale): static;

    public function setReferenceableFallbackLocale(string $locale): static;
}
