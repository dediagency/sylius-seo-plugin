<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Builder;

use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

interface MetadataDirectorInterface
{
    /** @param Metadata[] $metadata */
    public function buildHierarchical(array $metadata): Metadata;
}
