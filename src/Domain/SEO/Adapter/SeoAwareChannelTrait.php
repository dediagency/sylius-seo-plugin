<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

use Doctrine\ORM\Mapping as ORM;

trait SeoAwareChannelTrait
{
    #[ORM\Column(type: 'string', name: 'google_analytics_code', nullable: true)]
    protected ?string $googleAnalyticsCode = null;

    #[ORM\Column(type: 'string', name: 'google_tag_manager_id', nullable: true)]
    protected ?string $googleTagManagerId = null;

    public function getGoogleAnalyticsCode(): ?string
    {
        return $this->googleAnalyticsCode;
    }

    public function setGoogleAnalyticsCode(?string $code): void
    {
        $this->googleAnalyticsCode = $code;
    }

    public function getGoogleTagManagerId(): ?string
    {
        return $this->googleTagManagerId;
    }

    public function setGoogleTagManagerId(?string $id): void
    {
        $this->googleTagManagerId = $id;
    }
}
