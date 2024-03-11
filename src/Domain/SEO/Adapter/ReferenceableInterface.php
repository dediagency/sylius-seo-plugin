<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

interface ReferenceableInterface extends MetadataTagInterface
{
    public function getReferenceableContent(): MetadataTagInterface;

    public function setReferenceableLocale(string $locale): static;

    public function setReferenceableFallbackLocale(string $locale): static;
}
