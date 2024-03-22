<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Transformer;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

class SEOContentToMetadataTransformer implements SEOContentToMetadataTransformerInterface
{
    public function transform(SEOContentInterface $seo): Metadata
    {
        return new Metadata(
            $seo->getType(),
            !$seo->isNotIndexable(),
            $seo->getMetadataTitle(),
            $seo->getMetadataDescription(),
            $seo->getOpenGraphMetadataTitle(),
            $seo->getOpenGraphMetadataDescription(),
            $seo->getOpenGraphMetadataUrl(),
            $seo->getOpenGraphMetadataType(),
            $seo->getOpenGraphMetadataImage(),
        );
    }
}
