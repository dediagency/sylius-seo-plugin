<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

trait ReferenceableTrait
{
    protected ?ReferenceableInterface $referenceableContent = null;

    public function getReferenceableContent(): ReferenceableInterface
    {
        if (null === $this->referenceableContent) {
            $referenceableContent = $this->createReferenceableContent();
            $this->referenceableContent = $referenceableContent;
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

    public function getOpenGraphMetadataPrice(): ?string
    {
        return $this->getReferenceableContent()->getOpenGraphMetadataPrice();
    }

    public function getOpenGraphMetadataCurrency(): ?string
    {
        return $this->getOpenGraphMetadataCurrency();
    }

    abstract protected function createReferenceableContent(): ReferenceableInterface;
}
