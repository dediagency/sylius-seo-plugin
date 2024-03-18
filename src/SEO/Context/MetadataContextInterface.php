<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Context;

use Dedi\SyliusSEOPlugin\SEO\Model\Metadata;

interface MetadataContextInterface
{
    public function getMetadata(): Metadata;
}
