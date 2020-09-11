# Installation

Run `composer require dedi/plugin-seo-plugin`

Change your `config/bundles.php` file to add the line for the plugin :

```php
<?php

return [
    //..
    Dedi\SyliusSEOPlugin\DediSyliusSEOPlugin::class => ['all' => true],
];
```

Create `dedi_sylius_seo_plugin.yaml` file into `config/packages` folder to import required config

```yaml
# config/packages/dedi_sylius_seo_plugin.yaml

imports:
    - { resource: "@DediSyliusSEOPlugin/Resources/config/config.yaml" }
```

# Usage

To add SEO content administration for a Sylius resource, please follow this cookbook.

### 1 - Implement ReferenceableInterface into your Entity

For example with Sylius Product Entity.

```php

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableTrait;
use Dedi\SyliusSEOPlugin\Entity\SEOContent;

class Product implements ReferenceableInterface
{
    use ReferenceableTrait;

    protected function createReferenceableContent(): ReferenceableInterface
    {
        return new SEOContent();
    }
}
```

ReferenceableTrait add all required methods. All methods available here : [src/Domain/SEO/Adapter/ReferenceableTrait.php](src/Domain/SEO/Adapter/ReferenceableTrait.php)

### 2 - Extend your form type extension with AbstractReferenceableTypeExtension

For example with FormTypeExtension of Sylius Product Entity.

```php
use Dedi\SyliusSEOPlugin\Form\Extension\AbstractReferenceableTypeExtension;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;

class ProductTypeExtension extends AbstractReferenceableTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}
```

### 3 - Edit admin template to add SEO content form

For example with Sylius Product administration

```twig
{# template/bundles/SyliusAdminBundle/Product/Tab/_detail.html.twig #}

...
<div class="ui hidden divider"></div>

<div class="ui segment">
    {{ form_row(form.referenceableContent) }}
</div>
```

### 4 - Call SEO header events

For example into layout template with Product's referenceable content

```twig
<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {% block title %}
        {{ sylius_template_event('dedi_sylius_seo_plugin.title', { resource: product }) }}
    {% endblock %}

    {% block metatags %}
        {{ sylius_template_event('dedi_sylius_seo_plugin.metatags', { resource: product }) }}
    {% endblock %}

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
</head>
```

### Bonus - Set default values for SEO informations

To set default values for all SEO metadata, override `ReferenceableTrait` methods like this :

```php
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableTrait;
use Dedi\SyliusSEOPlugin\Entity\SEOContent;

class Product implements ReferenceableInterface
{
    use ReferenceableTrait {
        getMetadataTitle as getBaseMetadataTitle;
        getMetadataDescription as getBaseMetadataDescription;
    }

    public function getMetadataTitle(): ?string
    {
        if (is_null($this->getReferenceableContent()->getMetadataTitle())) {
            return $this->getName();
        }

        return $this->getBaseMetadataTitle();
    }

    public function getMetadataDescription(): ?string
    {
        if (is_null($this->getReferenceableContent()->getMetadataDescription())) {
            return $this->getShortDescription();
        }

        return $this->getBaseMetadataDescription();
    }

    protected function createReferenceableContent(): ReferenceableInterface
    {
        return new SEOContent();
    }
}
```
