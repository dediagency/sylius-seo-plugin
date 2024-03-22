<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslatableTrait;

class SEOContent implements SEOContentInterface
{
    protected ?int $id = null;

    protected ?string $openGraphMetadataType = null;

    /** @var Collection<int, SEOContentRobotInterface> */
    protected Collection $robots;

    protected ?string $type = null;

    protected ?ReferenceableInterface $product = null;

    protected ?ReferenceableInterface $taxon = null;

    protected ?ReferenceableInterface $channel = null;

    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    public function __construct()
    {
        $this->initializeTranslationsCollection();
        $this->robots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isNotIndexable(): bool
    {
        /** @var SEOContentRobotInterface|false $robot */
        $robot = $this->getRobots()->filter(function (SEOContentRobotInterface $robot) {
            return $robot->getLocale() === $this->currentLocale;
        })->first();

        if (false !== $robot) {
            return $robot->isNotIndexable();
        }

        return false;
    }

    public function setNotIndexable(bool $notIndexable): static
    {
        $this->getTranslation()->setNotIndexable($notIndexable);

        return $this;
    }

    public function getMetadataTitle(): ?string
    {
        return $this->getTranslation()->getMetadataTitle();
    }

    public function setMetadataTitle(?string $title): static
    {
        $this->getTranslation()->setMetadataTitle($title);

        return $this;
    }

    public function getMetadataDescription(): ?string
    {
        return $this->getTranslation()->getMetadataDescription();
    }

    public function setMetadataDescription(?string $description): static
    {
        $this->getTranslation()->setMetadataDescription($description);

        return $this;
    }

    public function getOpenGraphMetadataTitle(): ?string
    {
        return $this->getTranslation()->getOpenGraphMetadataTitle();
    }

    public function setOpenGraphMetadataTitle(?string $title): static
    {
        $this->getTranslation()->setOpenGraphMetadataTitle($title);

        return $this;
    }

    public function getOpenGraphMetadataDescription(): ?string
    {
        return $this->getTranslation()->getOpenGraphMetadataDescription();
    }

    public function setOpenGraphMetadataDescription(?string $description): static
    {
        $this->getTranslation()->setOpenGraphMetadataDescription($description);

        return $this;
    }

    public function getOpenGraphMetadataUrl(): ?string
    {
        return $this->getTranslation()->getOpenGraphMetadataUrl();
    }

    public function setOpenGraphMetadataUrl(?string $url): static
    {
        $this->getTranslation()->setOpenGraphMetadataUrl($url);

        return $this;
    }

    public function getOpenGraphMetadataType(): ?string
    {
        return $this->openGraphMetadataType;
    }

    public function setOpenGraphMetadataType(?string $openGraphMetadataType): static
    {
        $this->openGraphMetadataType = $openGraphMetadataType;

        return $this;
    }

    public function getOpenGraphMetadataImage(): ?string
    {
        return $this->getTranslation()->getOpenGraphMetadataImage();
    }

    public function setOpenGraphMetadataImage(?string $path): static
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRobot(): ?SEOContentRobotInterface
    {
        /** @var SEOContentRobotInterface|false $robot */
        $robot = $this->getRobots()->filter(function (SEOContentRobotInterface $robot) {
            return $robot->getLocale() === $this->currentLocale;
        })->first();

        if (false !== $robot) {
            return $robot;
        }

        return null;
    }

    public function getRobots(): Collection
    {
        return $this->robots;
    }

    public function addRobot(SEOContentRobotInterface $robot): static
    {
        if (!$this->robots->contains($robot)) {
            $robot->setSeoContent($this);
            $this->robots->add($robot);
        }

        return $this;
    }

    public function removeRobot(SEOContentRobotInterface $robot): static
    {
        if ($this->robots->contains($robot)) {
            $robot->setSeoContent(null);
            $this->robots->removeElement($robot);
        }

        return $this;
    }

    public function getProduct(): ?ReferenceableInterface
    {
        return $this->product;
    }

    public function setProduct(?ReferenceableInterface $product): static
    {
        if (null !== $this->product) {
            $this->product->setReferenceableContent(null);
        }
        if (null !== $product) {
            $product->setReferenceableContent($this);
        }
        $this->product = $product;

        return $this;
    }

    public function getTaxon(): ?ReferenceableInterface
    {
        return $this->taxon;
    }

    public function setTaxon(?ReferenceableInterface $taxon): static
    {
        if (null !== $this->taxon) {
            $this->taxon->setReferenceableContent(null);
        }
        if (null !== $taxon) {
            $taxon->setReferenceableContent($this);
        }
        $this->taxon = $taxon;

        return $this;
    }

    public function getChannel(): ?ReferenceableInterface
    {
        return $this->channel;
    }

    public function setChannel(?ReferenceableInterface $channel): static
    {
        if (null !== $this->channel) {
            $this->channel->setReferenceableContent(null);
        }
        if (null !== $channel) {
            $channel->setReferenceableContent($this);
        }
        $this->channel = $channel;

        return $this;
    }

    protected function createTranslation(): SEOContentTranslation
    {
        return new SEOContentTranslation();
    }
}
