<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Adapter;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;

trait ReferenceableTrait
{
    protected ?string $referenceableLocale = null;

    protected ?string $referenceableFallbackLocale = null;

    protected ?SEOContentInterface $referenceableContent = null;

    public function getReferenceableContent(): SEOContentInterface
    {
        if (null === $this->referenceableContent) {
            $this->referenceableContent = $this->createReferenceableContent();
            if (null === $this->referenceableLocale) {
                if (property_exists($this, 'currentLocale')) {
                    $this->referenceableLocale = $this->currentLocale;
                }
            }
            if (null === $this->referenceableFallbackLocale) {
                if (property_exists($this, 'fallbackLocale')) {
                    $this->referenceableFallbackLocale = $this->fallbackLocale;
                }
            }
            $this->referenceableContent->setCurrentLocale($this->referenceableLocale);
            $this->referenceableContent->setFallbackLocale($this->referenceableFallbackLocale);
        }

        return $this->referenceableContent;
    }

    public function setReferenceableContent(?SEOContentInterface $referenceableContent): static
    {
        $this->referenceableContent = $referenceableContent;

        return $this;
    }

    public function isNotIndexable(): bool
    {
        return $this->getReferenceableContent()->isNotIndexable();
    }

    public function getMetadataTitle(): ?string
    {
        return $this->getReferenceableContent()->getMetadataTitle();
    }

    public function getMetadataDescription(): ?string
    {
        return $this->getReferenceableContent()->getMetadataDescription();
    }

    public function getOpenGraphMetadataTitle(): ?string
    {
        return $this->getReferenceableContent()->getOpenGraphMetadataTitle();
    }

    public function getOpenGraphMetadataDescription(): ?string
    {
        return $this->getReferenceableContent()->getOpenGraphMetadataDescription();
    }

    public function getOpenGraphMetadataUrl(): ?string
    {
        return $this->getReferenceableContent()->getOpenGraphMetadataUrl();
    }

    public function getOpenGraphMetadataType(): ?string
    {
        return $this->getReferenceableContent()->getOpenGraphMetadataType();
    }

    public function getOpenGraphMetadataImage(): ?string
    {
        return $this->getReferenceableContent()->getOpenGraphMetadataImage();
    }

    public function setReferenceableLocale(string $locale): static
    {
        $this->referenceableLocale = $locale;

        return $this;
    }

    public function setReferenceableFallbackLocale(string $locale): static
    {
        $this->referenceableFallbackLocale = $locale;

        return $this;
    }

    abstract protected function createReferenceableContent(): SEOContentInterface;
}
