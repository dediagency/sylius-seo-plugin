<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/** @extends FactoryInterface<SEOContentInterface> */
interface SEOContentFactoryInterface extends FactoryInterface
{
    public function createTyped(string $type): SEOContentInterface;
}
