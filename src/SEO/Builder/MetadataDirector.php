<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Builder;

use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

class MetadataDirector implements MetadataDirectorInterface
{
    public function __construct(private MetadataBuilderInterface $builder)
    {
    }

    /** @param Metadata[] $metadata */
    public function buildHierarchical(array $metadata): Metadata
    {
        $this->builder->reset();

        foreach ($metadata as $metadatum) {
            $this->builder->setIndexable($metadatum->indexable);
            $this->builder->setTitle($metadatum->title);
            $this->builder->setDescription($metadatum->description);
            $this->builder->setOgTitle($metadatum->ogTitle);
            $this->builder->setOgDescription($metadatum->ogDescription);
            $this->builder->setOgUrl($metadatum->ogUrl);
            $this->builder->setOgImage($metadatum->ogImage);
            $this->builder->setOgType($metadatum->ogType);
        }

        return $this->builder->getMetadata();
    }
}
