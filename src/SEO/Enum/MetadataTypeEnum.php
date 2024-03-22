<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Enum;

final class MetadataTypeEnum
{
    public const PRODUCT = 'product';

    public const TAXON = 'taxon';

    public const CHANNEL = 'channel';

    public const URI = 'uri';

    private function __construct()
    {
    }
}
