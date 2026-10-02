<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Transformer;

use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

interface ReferenceableToMetadataTransformerInterface
{
    public function transform(ReferenceableInterface $referenceable): Metadata;
}
