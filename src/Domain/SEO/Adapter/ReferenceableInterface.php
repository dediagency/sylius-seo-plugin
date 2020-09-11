<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

interface ReferenceableInterface
{
    public function isNotIndexable(): bool;

    public function getMetadataTitle(): ?string;

    public function getMetadataDescription(): ?string;

    public function getOpenGraphMetadataTitle(): ?string;

    public function getOpenGraphMetadataDescription(): ?string;

    public function getOpenGraphMetadataUrl(): ?string;

    public function getOpenGraphMetadataType(): ?string;

    public function getOpenGraphMetadataImage(): ?string;
}
