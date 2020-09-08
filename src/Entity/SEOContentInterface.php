<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface SEOContentInterface extends ReferenceableInterface, TranslatableInterface, ResourceInterface
{
}
