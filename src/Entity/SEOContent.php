<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\TranslatableTrait;

class SEOContent implements SEOContentInterface
{
    protected ?int $id = null;

    #[ORM\OneToOne(targetEntity: ReferenceableInterface::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    protected ?ReferenceableInterface $referenceableContent = null;

    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isNotIndexable(): bool
    {
        return $this->getTranslation()->isNotIndexable();
    }

    public function setNotIndexable(bool $notIndexable): self
    {
        $this->getTranslation()->setNotIndexable($notIndexable);

        return $this;
    }

    public function getMetadataTitle(): ?string
    {
        return $this->getTranslation()->getMetadataTitle();
    }

    public function setMetadataTitle(?string $title): self
    {
        $this->getTranslation()->setMetadataTitle($title);

        return $this;
    }

    public function getMetadataDescription(): ?string
    {
        return $this->getTranslation()->getMetadataDescription();
    }

    public function setMetadataDescription(?string $description): self
    {
        $this->getTranslation()->setMetadataDescription($description);

        return $this;
    }

    public function getOpenGraphMetadataTitle(): ?string
    {
        return $this->getTranslation()->getOpenGraphMetadataTitle();
    }

    public function setOpenGraphMetadataTitle(?string $title): self
    {
        $this->getTranslation()->setOpenGraphMetadataTitle($title);

        return $this;
    }

    public function getOpenGraphMetadataDescription(): ?string
    {
        return $this->getTranslation()->getOpenGraphMetadataDescription();
    }

    public function setOpenGraphMetadataDescription(?string $description): self
    {
        $this->getTranslation()->setOpenGraphMetadataDescription($description);

        return $this;
    }

    public function getOpenGraphMetadataUrl(): ?string
    {
        return $this->getTranslation()->getOpenGraphMetadataUrl();
    }

    public function setOpenGraphMetadataUrl(?string $url): self
    {
        $this->getTranslation()->setOpenGraphMetadataUrl($url);

        return $this;
    }

    public function getOpenGraphMetadataType(): ?string
    {
        return $this->getTranslation()->getOpenGraphMetadataType();
    }

    public function setOpenGraphMetadataType(?string $type): self
    {
        $this->getTranslation()->setOpenGraphMetadataType($type);

        return $this;
    }

    public function getOpenGraphMetadataImage(): ?string
    {
        return $this->getTranslation()->getOpenGraphMetadataImage();
    }

    public function setOpenGraphMetadataImage(?string $path): self
    {
        $this->getTranslation()->setOpenGraphMetadataImage($path);

        return $this;
    }

    public function getTranslation(?string $locale = null): SEOContentTranslation
    {
        /** @var SEOContentTranslation $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): SEOContentTranslation
    {
        return new SEOContentTranslation();
    }

    public function getReferenceableContent(): ?ReferenceableInterface
    {
        return $this->referenceableContent;
    }

    public function setReferenceableContent(?ReferenceableInterface $referenceableContent): self
    {
        $this->referenceableContent = $referenceableContent;

        return $this;
    }
}
