<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Transformer;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

interface SEOContentToMetadataTransformerInterface
{
    public function transform(SEOContentInterface $SEOContent, string $origin): Metadata;
}
