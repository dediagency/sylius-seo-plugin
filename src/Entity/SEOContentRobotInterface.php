<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface SEOContentRobotInterface extends ResourceInterface
{
    public function getSeoContent(): ?SEOContentInterface;

    public function setSeoContent(?SEOContentInterface $SEOContent): static;

    public function isNotIndexable(): bool;

    public function setNotIndexable(bool $notIndexable): static;

    public function getLocale(): ?string;

    public function setLocale(?string $locale): static;
}
