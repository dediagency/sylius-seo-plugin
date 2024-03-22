<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Model;

class Metadata
{
    public function __construct(
        public ?string $origin = null,
        public ?bool $indexable = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $ogTitle = null,
        public ?string $ogDescription = null,
        public ?string $ogUrl = null,
        public ?string $ogType = null,
        public ?string $ogImage = null,
    ) {
    }
}
