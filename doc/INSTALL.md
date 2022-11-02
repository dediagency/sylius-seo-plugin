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

## Website SEO optimizations

Call the SEO links event in your main layout header. This will automatically add a `<link rel="canonical>` HTML tag to you website's pages.

```twig
{# layout.html.twig #}
<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {% block metatags %}
        {{ sylius_template_event('dedi_sylius_seo_plugin.rich_snippets') }}
    {% endblock %}
    
    {% block links %}
        {{ sylius_template_event('dedi_sylius_seo_plugin.links') }}
    {% endblock %}

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
</head>
```

## SEO Usage for Product and Channel entities

The plugin has pre-configuration for Product and Channel entities.

You have to add `ReferenceableInterface` into Product and Channel classes

```php
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableTrait;
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct implements ReferenceableInterface
{
    use ReferenceableTrait;

    protected function createReferenceableContent(): ReferenceableInterface
    {
        return new SEOContent();
    }
}
```

```php
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableTrait;
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Sylius\Component\Core\Model\Channel as BaseChannel;

class Channel extends BaseChannel implements ReferenceableInterface
{
    use ReferenceableTrait;

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
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetProductSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetProductSubjectTrait;

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
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;

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
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\SeoAwareChannelInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\SeoAwareChannelTrait;

class Channel extends BaseChannel implements SeoAwareChannelInterface
{
    use SeoAwareChannelTrait;

    // ...
}
```

## Add twig events

The @SyliusShop/layout.html.twig should be overridden in order to add `dedi_sylius_seo_plugin` events in the `<head>` section of your page

Those events will load `<title>`, Open Graph metadata and Rich Snippets in you pages based on the current resource

>Note : it is important to override the default layout.html.twig and not just extend it.
>
> In the default layout, the line `<title>{% block title %}Sylius{% endblock %}</title>` will result in non valid HTML when redeclaring the `block title`

```twig
{# templates/bundles/SyliusShopBundle/layout.html.twig #}
<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {% block title %}
        {{ sylius_template_event('dedi_sylius_seo_plugin.title', { resource: product ?? sylius.channel }) }}
    {% endblock %}

    {% block metatags %}
        {{ sylius_template_event('dedi_sylius_seo_plugin.metatags', { resource: product ?? sylius.channel }) }}
        {{ sylius_template_event('dedi_sylius_seo_plugin.rich_snippets') }}
    {% endblock %}

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    ...
    {# rest of vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/Resources/views/layout.html.twig #}
    ... 
</head>
```

### Create migration

Create migration, review and execute them 

```
bin/console doctrine:migration:diff
bin/console doctrine:migration:migrate
```

### Bonus

- [Learn how to add SEO bloc for custom entity](doc/SEO_CUSTOM.md);
- [Learn how to create new RichSnippets](doc/RICH_SNIPPETS.md)

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
