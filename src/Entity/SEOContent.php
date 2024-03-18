<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslatableTrait;

class SEOContent implements SEOContentInterface
{
    protected ?int $id = null;

    protected ?string $openGraphMetadataType = null;

    /** @var Collection<int, SEOContentRobotInterface> */
    protected Collection $robots;

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
        return $this->openGraphMetadataType;
    }

    public function setOpenGraphMetadataType(?string $openGraphMetadataType): self
    {
        $this->openGraphMetadataType = $openGraphMetadataType;

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
}
