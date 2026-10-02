<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

class SEOContentRobot implements SEOContentRobotInterface
{
    protected ?int $id = null;

    protected ?SEOContentInterface $seoContent = null;

    protected bool $notIndexable = false;

    protected ?string $locale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeoContent(): ?SEOContentInterface
    {
        return $this->seoContent;
    }

    public function setSeoContent(?SEOContentInterface $seoContent): static
    {
        $this->seoContent = $seoContent;

        return $this;
    }

    public function isNotIndexable(): bool
    {
        return $this->notIndexable;
    }

    public function setNotIndexable(bool $notIndexable): static
    {
        $this->notIndexable = $notIndexable;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }
}
