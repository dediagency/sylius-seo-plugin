<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Transformer;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

class SEOContentToMetadataTransformer implements SEOContentToMetadataTransformerInterface
{
    public function transform(SEOContentInterface $SEOContent, string $origin): Metadata
    {
        return new Metadata(
            $origin,
            !$SEOContent->isNotIndexable(),
            $SEOContent->getMetadataTitle(),
            $SEOContent->getMetadataDescription(),
            $SEOContent->getOpenGraphMetadataTitle(),
            $SEOContent->getOpenGraphMetadataDescription(),
            $SEOContent->getOpenGraphMetadataUrl(),
            $SEOContent->getOpenGraphMetadataType(),
            $SEOContent->getOpenGraphMetadataImage(),
        );
    }
}
