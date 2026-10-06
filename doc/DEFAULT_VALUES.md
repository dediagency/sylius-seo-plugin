# Set default values for SEO metadata

When a metadata field is left empty in the admin, you can compute a default value. Override the `ReferenceableTrait` getters in your entity:

```php
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableProductTrait;
use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct implements ReferenceableInterface
{
    use ReferenceableProductTrait {
        getMetadataTitle as getBaseMetadataTitle;
        getMetadataDescription as getBaseMetadataDescription;
        getOpenGraphMetadataImage as getBaseOpenGraphMetadataImage;
    }

    public function getMetadataTitle(): ?string
    {
        if (null === $this->getReferenceableContent()->getMetadataTitle()) {
            return $this->getName();
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
            false !== $this->getImages()->first()
        ) {
            return $this->getImages()->first()->getPath();
        }

        return $this->getBaseOpenGraphMetadataImage();
    }

    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }
}
```

The SEO content locale is set automatically when the entity is loaded, so `getReferenceableContent()` returns the values of the current locale.

Fields that are still empty fall back to the channel metadata (see [How the page metadata is resolved](FEATURES.md#how-the-page-metadata-is-resolved)).

Overridable getters: `getMetadataTitle()`, `getMetadataDescription()`, `getOpenGraphMetadataTitle()`, `getOpenGraphMetadataDescription()`, `getOpenGraphMetadataUrl()`, `getOpenGraphMetadataType()`, `getOpenGraphMetadataImage()`, `isNotIndexable()`.
