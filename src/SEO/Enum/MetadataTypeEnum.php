<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Enum;

enum MetadataTypeEnum: string
{
    case PRODUCT = 'product';
    case TAXON = 'taxon';
    case CHANNEL = 'channel';
    case URI = 'uri';
}
