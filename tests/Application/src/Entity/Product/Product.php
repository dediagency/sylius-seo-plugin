<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Entity\Product;

use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductSubjectTrait;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableProductTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Product as BaseProduct;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="sylius_product")
 */
class Product extends BaseProduct implements ReferenceableInterface, RichSnippetProductSubjectInterface
{
    use ReferenceableProductTrait {
        getMetadataTitle as getBaseMetadataTitle;
        getMetadataDescription as getBaseMetadataDescription;
        getOpenGraphMetadataImage as getBaseOpenGraphMetadataImage;
    }
    use RichSnippetProductSubjectTrait;

    public function getMetadataTitle(): ?string
    {
        if (null === $this->getReferenceableContent()->getMetadataTitle()) {
            return null === $this->getMainTaxon() ? $this->getName() :
                $this->getName() . ' | ' . $this->getMainTaxon()->getName();
        }

        return $this->getBaseMetadataTitle();
    }

    public function getMetadataDescription(): ?string
    {
        if (null === $this->getReferenceableContent()->getMetadataDescription()) {
            return $this->getShortDescription();
        }

        return $this->getBaseMetadataDescription();
    }

    public function getOpenGraphMetadataImage(): ?string
    {
        if (
            null === $this->getReferenceableContent()->getOpenGraphMetadataImage() &&
            null != $this->getImages()->first()
        ) {
            return $this->getImages()->first()->getPath();
        }

        return $this->getBaseOpenGraphMetadataImage();
    }

    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }

    public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface
    {
        return $this->getMainTaxon();
    }

    public function getRichSnippetSubjectType(): string
    {
        return 'product';
    }
}
