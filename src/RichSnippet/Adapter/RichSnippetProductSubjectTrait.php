<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Adapter;

use Doctrine\ORM\Mapping as ORM;

trait RichSnippetProductSubjectTrait
{
    #[ORM\Column(name: 'seo_brand', type: 'string', nullable: true)]
    protected ?string $SEOBrand = null;

    #[ORM\Column(name: 'seo_gtin8', type: 'string', length: 8, nullable: true)]
    protected ?string $SEOGtin8 = null;

    #[ORM\Column(name: 'seo_gtin13', type: 'string', length: 13, nullable: true)]
    protected ?string $SEOGtin13 = null;

    #[ORM\Column(name: 'seo_gtin14', type: 'string', length: 14, nullable: true)]
    protected ?string $SEOGtin14 = null;

    #[ORM\Column(name: 'seo_mpn', type: 'string', nullable: true)]
    protected ?string $SEOMpn = null;

    #[ORM\Column(name: 'seo_isbn', type: 'string', nullable: true)]
    protected ?string $SEOIsbn = null;

    #[ORM\Column(name: 'seo_sku', type: 'string', nullable: true)]
    protected ?string $SEOSku = null;

    #[ORM\Column(name: 'seo_offer_aggregated', type: 'boolean', nullable: false, options: ['default' => 0])]
    protected bool $SEOOfferAggregated = false;

    public function getSEOBrand(): ?string
    {
        return $this->SEOBrand;
    }

    public function setSEOBrand(?string $SEOBrand): void
    {
        $this->SEOBrand = $SEOBrand;
    }

    public function getSEOGtin8(): ?string
    {
        return $this->SEOGtin8;
    }

    public function setSEOGtin8(?string $SEOGtin8): void
    {
        $this->SEOGtin8 = $SEOGtin8;
    }

    public function getSEOGtin13(): ?string
    {
        return $this->SEOGtin13;
    }

    public function setSEOGtin13(?string $SEOGtin13): void
    {
        $this->SEOGtin13 = $SEOGtin13;
    }

    public function getSEOGtin14(): ?string
    {
        return $this->SEOGtin14;
    }

    public function setSEOGtin14(?string $SEOGtin14): void
    {
        $this->SEOGtin14 = $SEOGtin14;
    }

    public function getSEOMpn(): ?string
    {
        return $this->SEOMpn;
    }

    public function setSEOMpn(?string $SEOMpn): void
    {
        $this->SEOMpn = $SEOMpn;
    }

    public function getSEOIsbn(): ?string
    {
        return $this->SEOIsbn;
    }

    public function setSEOIsbn(?string $SEOIsbn): void
    {
        $this->SEOIsbn = $SEOIsbn;
    }

    public function getSEOSku(): ?string
    {
        return $this->SEOSku;
    }

    public function setSEOSku(?string $SEOSku): void
    {
        $this->SEOSku = $SEOSku;
    }
}
