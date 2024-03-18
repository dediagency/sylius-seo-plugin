<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Dedi\SyliusSEOPlugin\SEO\Adapter\MetadataAwareInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface SEOContentInterface extends MetadataAwareInterface, TranslatableInterface, ResourceInterface
{
    public function getOpenGraphMetadataType(): ?string;

    public function setOpenGraphMetadataType(?string $openGraphMetadataType): self;

    /** @return Collection<int, SEOContentRobotInterface> */
    public function getRobots(): Collection;

    public function getRobot(): ?SEOContentRobotInterface;

    public function addRobot(SEOContentRobotInterface $robot): static;

    public function removeRobot(SEOContentRobotInterface $robot): static;
}
