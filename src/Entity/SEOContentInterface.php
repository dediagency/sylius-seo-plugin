<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\MetadataTagInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface SEOContentInterface extends MetadataTagInterface, TranslatableInterface, ResourceInterface
{
}
