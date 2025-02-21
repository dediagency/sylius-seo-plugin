# Installation

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

    protected function createReferenceableContent(): ReferenceableInterface
    {
        return new SEOContent();
    }
}
```

```php
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableProductTrait;
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

    protected function createReferenceableContent(): ReferenceableInterface
    {
        return new SEOContent();
    }
}
```

```php
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
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
    
    protected function createReferenceableContent(): ReferenceableInterface
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

Make your `Product` and `Taxon` classes implement the `RichSnippetSubjectInterface` interface.

```php
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductSubjectTrait;use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;

class Product extends BaseProduct implements RichSnippetSubjectInterface
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

### Add Google Analytics Console Configuration

You have to add `SeoAwareChannelInterface` for Channel Entity

```php
use Dedi\SyliusSEOPlugin\SEO\Adapter\SeoAwareChannelInterface;use Dedi\SyliusSEOPlugin\SEO\Adapter\SeoAwareChannelTrait;

class Channel extends BaseChannel implements SeoAwareChannelInterface
{
    use SeoAwareChannelTrait;

    // ...
}
```

### Create migration

Create migration, review and execute them 

```bash
bin/console doctrine:migration:diff
bin/console doctrine:migration:migrate
```

### Guide

- [Learn how to add SEO bloc for custom entity](SEO_CUSTOM.md);
- [Learn how to create new RichSnippets](RICH_SNIPPETS.md)
- [Learn how to set default values for your metadata](DEFAULT_VALUES.md)
