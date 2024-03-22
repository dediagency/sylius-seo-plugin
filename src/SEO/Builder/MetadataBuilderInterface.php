<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Builder;

use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

interface MetadataBuilderInterface
{
    public function reset(): void;

    public function setIndexable(?bool $indexable = null): void;

    public function setTitle(?string $title = null): void;

    public function setDescription(?string $description = null): void;

    public function setOgTitle(?string $ogTitle = null): void;

    public function setOgDescription(?string $ogDescription = null): void;

    public function setOgUrl(?string $ogUrl = null): void;

    public function setOgType(?string $ogType = null): void;

    public function setOgImage(?string $ogImage = null): void;

    public function getMetadata(): Metadata;
}
