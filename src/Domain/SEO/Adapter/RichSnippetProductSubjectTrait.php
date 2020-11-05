<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

use Doctrine\ORM\Mapping as ORM;

trait RichSnippetProductSubjectTrait
{
    /**
     * @ORM\Column(type="string", name="brand", nullable=true)
     */
    protected ?string $brand = null;

    /**
     * @ORM\Column(type="string", length=8, name="gtin8", nullable=true)
     */
    protected ?string $gtin8 = null;

    /**
     * @ORM\Column(type="string", length=13, name="gtin13", nullable=true)
     */
    protected ?string $gtin13 = null;

    /**
     * @ORM\Column(type="string", length=14, name="gtin14", nullable=true)
     */
    protected ?string $gtin14 = null;

    /**
     * @ORM\Column(type="string", name="mpn", nullable=true)
     */
    protected ?string $mpn = null;

    /**
     * @ORM\Column(type="string", name="isbn", nullable=true)
     */
    protected ?string $isbn = null;

    /**
     * @ORM\Column(type="string", name="sku", nullable=true)
     */
    protected ?string $sku = null;

    /**
     * @ORM\Column(type="boolean", name="offer_aggregated", nullable=false, options={"default" : 0})
     */
    protected bool $offerAggregated = false;

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getGtin8(): ?string
    {
        return $this->gtin8;
    }

    public function setGtin8(?string $gtin8): void
    {
        $this->gtin8 = $gtin8;
    }

    public function getGtin13(): ?string
    {
        return $this->gtin13;
    }

    public function setGtin13(?string $gtin13): void
    {
        $this->gtin13 = $gtin13;
    }

    public function getGtin14(): ?string
    {
        return $this->gtin14;
    }

    public function setGtin14(?string $gtin14): void
    {
        $this->gtin14 = $gtin14;
    }

    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    public function setMpn(?string $mpn): void
    {
        $this->mpn = $mpn;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): void
    {
        $this->isbn = $isbn;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): void
    {
        $this->sku = $sku;
    }
}
