<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Dedi\SyliusSEOPlugin\SEO\Adapter\MetadataAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface SEOContentTranslationInterface extends ResourceInterface, TranslationInterface, MetadataAwareInterface
{
    public function getUri(): ?string;

    public function setUri(?string $url): static;

    public function setMetadataTitle(?string $title): self;

    public function setMetadataDescription(?string $description): self;

    public function setOpenGraphMetadataTitle(?string $openGraphMetadataTitle): self;

    public function setOpenGraphMetadataDescription(?string $openGraphMetadataDescription): self;

    public function setOpenGraphMetadataUrl(?string $openGraphMetadataUrl): self;

    public function setOpenGraphMetadataImage(?string $openGraphMetadataImage): self;
}
