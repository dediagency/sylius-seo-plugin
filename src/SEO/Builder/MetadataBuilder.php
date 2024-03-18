<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Builder;

use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

class MetadataBuilder implements MetadataBuilderInterface
{
    private Metadata $metadata;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->metadata = new Metadata();
    }

    public function setIndexable(?bool $indexable = null): void
    {
        if (null === $this->metadata->indexable) {
            $this->metadata->indexable = $indexable;
        }
    }

    public function setTitle(?string $title = null): void
    {
        if (null === $this->metadata->title) {
            $this->metadata->title = $title;
        }
    }

    public function setDescription(?string $description = null): void
    {
        if (null === $this->metadata->description) {
            $this->metadata->description = $description;
        }
    }

    public function setOgTitle(?string $ogTitle = null): void
    {
        if (null === $this->metadata->ogTitle) {
            $this->metadata->ogTitle = $ogTitle;
        }
    }

    public function setOgDescription(?string $ogDescription = null): void
    {
        if (null === $this->metadata->ogDescription) {
            $this->metadata->ogDescription = $ogDescription;
        }
    }

    public function setOgUrl(?string $ogUrl = null): void
    {
        if (null === $this->metadata->ogUrl) {
            $this->metadata->ogUrl = $ogUrl;
        }
    }

    public function setOgType(?string $ogType = null): void
    {
        if (null === $this->metadata->ogType) {
            $this->metadata->ogType = $ogType;
        }
    }

    public function setOgImage(?string $ogImage = null): void
    {
        if (null === $this->metadata->ogImage) {
            $this->metadata->ogImage = $ogImage;
        }
    }

    public function getMetadata(): Metadata
    {
        $metadata = $this->metadata;

        $this->reset();

        return $metadata;
    }
}
