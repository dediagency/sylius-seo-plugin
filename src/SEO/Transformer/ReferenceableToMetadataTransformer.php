<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Transformer;

use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

class ReferenceableToMetadataTransformer implements ReferenceableToMetadataTransformerInterface
{
    public function transform(ReferenceableInterface $referenceable): Metadata
    {
        return new Metadata(
            $referenceable->getReferenceableContent()->getType(),
            !$referenceable->isNotIndexable(),
            $referenceable->getMetadataTitle(),
            $referenceable->getMetadataDescription(),
            $referenceable->getOpenGraphMetadataTitle(),
            $referenceable->getOpenGraphMetadataDescription(),
            $referenceable->getOpenGraphMetadataUrl(),
            $referenceable->getOpenGraphMetadataType(),
            $referenceable->getOpenGraphMetadataImage(),
        );
    }
}
