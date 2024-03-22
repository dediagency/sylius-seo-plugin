<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Dedi\SyliusSEOPlugin\SEO\Adapter\MetadataAwareInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
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

    public function getProduct(): ?ReferenceableInterface;

    public function setProduct(?ReferenceableInterface $product): static;

    public function getTaxon(): ?ReferenceableInterface;

    public function setTaxon(?ReferenceableInterface $taxon): static;

    public function getChannel(): ?ReferenceableInterface;

    public function setChannel(?ReferenceableInterface $channel): static;

    public function getType(): ?string;

    public function setType(?string $type): static;
}
