<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Application\src\Entity\Product;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\MetadataTagInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableTrait;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetProductSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetProductSubjectTrait;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Product as BaseProduct;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="sylius_product")
 */
class Product extends BaseProduct implements ReferenceableInterface, RichSnippetProductSubjectInterface
{
    use ReferenceableTrait {
        getMetadataTitle as getBaseMetadataTitle;
        getMetadataDescription as getBaseMetadataDescription;
        getOpenGraphMetadataImage as getBaseOpenGraphMetadataImage;
    }
    use RichSnippetProductSubjectTrait;

    public function getMetadataTitle(): ?string
    {
        if (null === $this->getReferenceableContent()->getMetadataTitle()) {
            $this->setCurrentLocale($this->getReferenceableContent()->getTranslation()->getLocale());

            return null === $this->getMainTaxon() ? $this->getName() :
                $this->getMainTaxon()->getName() . ' | ' . $this->getName();
        }

        return $this->getBaseMetadataTitle();
    }

    public function getMetadataDescription(): ?string
    {
        if (null === $this->getReferenceableContent()->getMetadataDescription()) {
            $this->setCurrentLocale($this->getReferenceableContent()->getTranslation()->getLocale());

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

    protected function createReferenceableContent(): MetadataTagInterface
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
