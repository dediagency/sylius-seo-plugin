<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\SEO\Adapter;

use Doctrine\ORM\Mapping as ORM;

trait SeoAwareChannelTrait
{
    #[ORM\Column(name: 'google_analytics_code', type: 'string', nullable: true)]
    protected ?string $googleAnalyticsCode = null;

    #[ORM\Column(name: 'google_tag_manager_id', type: 'string', nullable: true)]
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
