<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait RichSnippetProductSubjectTrait
{
    #[ORM\Column(type: 'string', name: 'seo_brand', nullable: true)]
    protected ?string $SEOBrand = null;

    #[ORM\Column(type: 'string', length: 8, name: 'seo_gtin8', nullable: true)]
    #[Assert\Length(max: 8)]
    protected ?string $SEOGtin8 = null;

    #[ORM\Column(type: 'string', length: 13, name: 'seo_gtin13', nullable: true)]
    #[Assert\Length(max: 13)]
    protected ?string $SEOGtin13 = null;

    #[ORM\Column(type: 'string', length: 14, name: 'seo_gtin14', nullable: true)]
    #[Assert\Length(max: 14)]
    protected ?string $SEOGtin14 = null;

    #[ORM\Column(type: 'string', name: 'seo_mpn', nullable: true)]
    protected ?string $SEOMpn = null;

    #[ORM\Column(type: 'string', name: 'seo_isbn', nullable: true)]
    protected ?string $SEOIsbn = null;

    #[ORM\Column(type: 'string', name: 'seo_sku', nullable: true)]
    protected ?string $SEOSku = null;

    #[ORM\Column(type: 'boolean', name: 'seo_offer_aggregated', nullable: false, options: ['default' => 0])]
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
