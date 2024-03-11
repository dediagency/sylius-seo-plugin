<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

trait ReferenceableTrait
{
    protected ?string $referenceableLocale = null;

    protected ?string $referenceableFallbackLocale = null;

    protected ?MetadataTagInterface $referenceableContent = null;

    public function getReferenceableContent(): MetadataTagInterface
    {
        if (null === $this->referenceableContent) {
            $this->referenceableContent = $this->createReferenceableContent();
            $this->referenceableContent->setCurrentLocale($this->referenceableLocale);
            $this->referenceableContent->setFallbackLocale($this->referenceableFallbackLocale);
        }

        return $this->referenceableContent;
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

    abstract protected function createReferenceableContent(): MetadataTagInterface;
}
