<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Entity\Channel;

use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableAwareInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTrait;
use Dedi\SyliusSEOPlugin\SEO\Adapter\SeoAwareChannelInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\SeoAwareChannelTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Channel as BaseChannel;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements ReferenceableAwareInterface, SeoAwareChannelInterface
{
    use ReferenceableTrait {
        getMetadataTitle as getBaseMetadataTitle;
        getMetadataDescription as getBaseMetadataDescription;
    }
    use SeoAwareChannelTrait;

    public function getMetadataTitle(): ?string
    {
        if (null === $this->getReferenceableContent()->getMetadataTitle()) {
            return $this->getName();
        }

        return $this->getBaseMetadataTitle();
    }

    public function getMetadataDescription(): ?string
    {
        if (null === $this->getReferenceableContent()->getMetadataDescription()) {
            return $this->getDescription();
        }

        return $this->getBaseMetadataDescription();
    }

    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }
}
