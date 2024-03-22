<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Sylius\Component\Resource\Model\AbstractTranslation;

class SEOContentTranslation extends AbstractTranslation implements SEOContentTranslationInterface
{
    protected ?int $id = null;

    /** @deprecated Use SEOContentRobot::$notIndexable instead */
    protected bool $notIndexable = false;

    protected ?string $uri = null;

    protected ?string $metadataTitle = null;

    protected ?string $metadataDescription = null;

    protected ?string $openGraphMetadataTitle = null;

    protected ?string $openGraphMetadataDescription = null;

    protected ?string $openGraphMetadataUrl = null;

    protected ?string $openGraphMetadataImage = null;

    /** @deprecated Use SEOContent::$openGraphMetadataType instead */
    protected ?string $openGraphMetadataType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isNotIndexable(): bool
    {
        return $this->notIndexable;
    }

    public function setNotIndexable(bool $notIndexable): self
    {
        $this->notIndexable = $notIndexable;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $url): static
    {
        $this->uri = $url;

        return $this;
    }

    public function getMetadataTitle(): ?string
    {
        return $this->metadataTitle;
    }

    public function setMetadataTitle(?string $title): self
    {
        $this->metadataTitle = $title;

        return $this;
    }

    public function getMetadataDescription(): ?string
    {
        return $this->metadataDescription;
    }

    public function setMetadataDescription(?string $description): self
    {
        $this->metadataDescription = $description;

        return $this;
    }

    public function getOpenGraphMetadataTitle(): ?string
    {
        return $this->openGraphMetadataTitle;
    }

    public function setOpenGraphMetadataTitle(?string $openGraphMetadataTitle): self
    {
        $this->openGraphMetadataTitle = $openGraphMetadataTitle;

        return $this;
    }

    public function getOpenGraphMetadataDescription(): ?string
    {
        return $this->openGraphMetadataDescription;
    }

    public function setOpenGraphMetadataDescription(?string $openGraphMetadataDescription): self
    {
        $this->openGraphMetadataDescription = $openGraphMetadataDescription;

        return $this;
    }

    public function getOpenGraphMetadataUrl(): ?string
    {
        return $this->openGraphMetadataUrl;
    }

    public function setOpenGraphMetadataUrl(?string $openGraphMetadataUrl): self
    {
        $this->openGraphMetadataUrl = $openGraphMetadataUrl;

        return $this;
    }

    public function getOpenGraphMetadataImage(): ?string
    {
        return $this->openGraphMetadataImage;
    }

    public function setOpenGraphMetadataImage(?string $openGraphMetadataImage): self
    {
        $this->openGraphMetadataImage = $openGraphMetadataImage;

        return $this;
    }

    public function getOpenGraphMetadataType(): ?string
    {
        return $this->openGraphMetadataType;
    }

    public function setOpenGraphMetadataType(?string $openGraphMetadataType): self
    {
        $this->openGraphMetadataType = $openGraphMetadataType;

        return $this;
    }
}
