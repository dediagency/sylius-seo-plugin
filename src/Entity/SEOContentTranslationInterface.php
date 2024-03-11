<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface SEOContentTranslationInterface extends ResourceInterface, TranslationInterface
{
    public function isNotIndexable(): bool;

    public function setNotIndexable(bool $indexable): self;

    public function getMetadataTitle(): ?string;

    public function setMetadataTitle(?string $title): self;

    public function getMetadataDescription(): ?string;

    public function setMetadataDescription(?string $description): self;

    public function getOpenGraphMetadataTitle(): ?string;

    public function setOpenGraphMetadataTitle(?string $openGraphMetadataTitle): self;

    public function getOpenGraphMetadataDescription(): ?string;

    public function setOpenGraphMetadataDescription(?string $openGraphMetadataDescription): self;

    public function getOpenGraphMetadataUrl(): ?string;

    public function setOpenGraphMetadataUrl(?string $openGraphMetadataUrl): self;

    public function getOpenGraphMetadataImage(): ?string;

    public function setOpenGraphMetadataImage(?string $openGraphMetadataImage): self;

    public function getOpenGraphMetadataType(): ?string;

    public function setOpenGraphMetadataType(?string $openGraphMetadataType): self;
}
