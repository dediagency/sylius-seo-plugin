<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

interface SeoAwareChannelInterface
{
    public function getGoogleAnalyticsCode(): ?string;

    public function setGoogleAnalyticsCode(?string $code): void;

    public function getGoogleTagManagerId(): ?string;

    public function setGoogleTagManagerId(?string $id): void;
}
