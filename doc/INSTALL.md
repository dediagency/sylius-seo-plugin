# Installation

Requires PHP 8.2+ and Sylius 2.0 to 2.3. For Sylius 1.x, use the 3.x version of the plugin.

Run `composer require dedi/sylius-seo-plugin --no-scripts`

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

Create `dedi_sylius_seo_plugin.yaml` file into `config/routes` folder to import required routes

```yaml
# config/routes/dedi_sylius_seo_plugin.yaml

dedi_sylius_seo_plugin:
  resource: "@DediSyliusSEOPlugin/Resources/config/routes.yaml"
```

## Override default layout template

The `@SyliusShop/shared/layout/base.html.twig` should be overridden to add plugin's twig hooks and functions in the `<head>` section of your page

>Note : it is important to override the default base.html.twig and not just extend it.
>
> To make sure the `<title>` is populated with plugin's data on every pages, we renamed the `{% block title %}` to `{% block seo_title %}`.<br>
> 
> Add `{% hook 'before_body' with { _prefixes: prefixes } %}` hook to add Google Tag Manager noscript iframe before just after the  `<body>` tag.

```html
{# templates/bundles/SyliusShopBundle/shared/layout/base.html.twig #}
<!DOCTYPE html>

<html lang="{{ app.request.locale|slice(0, 2) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!--- use dedi_sylius_seo_get_title() to fetch current page title -->
    <title>{% block seo_title %}{{ dedi_sylius_seo_get_title('Sylius') }}{% endblock %}</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes" name="viewport">

    {% block metatags %}
        {% hook '#metatags' with { _prefixes: prefixes } %}
    {% endblock %}

    {% block stylesheets %}
        {% hook '#stylesheets' with { _prefixes: prefixes } %}
    {% endblock %}

    {% hook 'head' with { _prefixes: prefixes } %}
</head>

<body data-route="{{ app.request.get('_route') }}">

    <!--- add 'before_body' hook for google tag manager noscript iframe -->
    {% hook 'before_body' with { _prefixes: prefixes } %}
```

## SEO usage for entities

The plugin has pre-configuration for Product, Taxon and Channel entities.

You have to implement `ReferenceableInterface` and use the related trait in Product, Taxon and Channel classes

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
    }
    
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

    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }
}
```

```php
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTaxonTrait;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;

class Taxon extends BaseTaxon implements ReferenceableInterface
{
    use ReferenceableTaxonTrait {
        getMetadataTitle as getBaseMetadataTitle;
        getMetadataDescription as getBaseMetadataDescription;
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
            return $this->getDescription();
        }

        return $this->getBaseMetadataDescription();
    }

    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }
}
```

```php
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableChannelTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

class Channel extends BaseChannel implements ReferenceableInterface
{
    use ReferenceableChannelTrait {
        getMetadataTitle as getBaseMetadataTitle;
        getMetadataDescription as getBaseMetadataDescription;
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
            return $this->getDescription();
        }

        return $this->getBaseMetadataDescription();
    }
    
    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }
}
```

## Rich Snippet usage for Product and Taxon entities

Plugin has pre-configuration rich snippet context for Product and Taxon entities.

Rich snippet available are :
- Breadcrumb for Product and Taxon entities
- Product for Product entity

Make your `Product` class implement `RichSnippetProductSubjectInterface` (required: the product SEO tab edits its brand and global identifier fields), and your `Taxon` class implement `RichSnippetSubjectInterface`.

```php
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductSubjectTrait;

class Product extends BaseProduct implements RichSnippetProductSubjectInterface
{
    use RichSnippetProductSubjectTrait;

    // ...
    public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface
    {
        return $this->getMainTaxon();
    }

    public function getRichSnippetSubjectType(): string
    {
        return 'product';
    }
}
```

```php
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;

class Taxon extends BaseTaxon implements RichSnippetSubjectInterface
{
    // ...
    public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface
    {
        return $this->getParent();
    }

    public function getRichSnippetSubjectType(): string
    {
        return 'taxon';
    }
}
```

## Product variant identifiers (optional)

GTIN, MPN and SKU identify a purchasable item, which in Sylius is the product variant. To manage them per variant, make your `ProductVariant` class implement `RichSnippetProductVariantSubjectInterface`.

```php
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductVariantSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductVariantSubjectTrait;

class ProductVariant extends BaseProductVariant implements RichSnippetProductVariantSubjectInterface
{
    use RichSnippetProductVariantSubjectTrait;
}
```

A SEO tab is then added to the product variant administration form. The identifiers are used in the product rich snippet: on the variant `Product` of the `ProductGroup` for products with several variants, and on the `Product` for products with one variant. See [Product rich snippet](FEATURES.md#product).

Products with several variants can also declare what their variants vary by (size, color, ...). See [Configuration](FEATURES.md#configuration).

## Add Google Analytics and Google Tag Manager configuration

To configure Google Analytics or Google Tag Manager per channel, make your `Channel` class implement `SeoAwareChannelInterface`.

```php
use Dedi\SyliusSEOPlugin\SEO\Adapter\SeoAwareChannelInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\SeoAwareChannelTrait;

class Channel extends BaseChannel implements ReferenceableInterface, SeoAwareChannelInterface
{
    use SeoAwareChannelTrait;

    // ...
}
```

## Noindex rules on taxon pages (optional)

To mark paginated or sorted taxon pages as not indexable, add the `_seo.no_index_no_follow_filter` option to the taxon route. See [Noindex filters on routes](FEATURES.md#noindex-filters-on-routes).

## Create migration

Create migration, review and execute them

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

## Guide

- [Features](FEATURES.md)
- [Learn how to add SEO bloc for custom entity](SEO_CUSTOM.md)
- [Learn how to create new RichSnippets](RICH_SNIPPETS.md)
- [Learn how to set default values for your metadata](DEFAULT_VALUES.md)
