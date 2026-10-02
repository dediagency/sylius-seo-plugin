# UPGRADE FROM `4.x` TO `5.0`

Version 5 is a large rewrite of the plugin. This guide only covers changes made to the plugin itself.
Upgrade your application to Sylius 2.0 first by following the official Sylius upgrade guide.

## Table of contents

1. [Requirements](#requirements)
2. [Configuration and routes](#configuration-and-routes)
3. [Entities](#entities)
4. [Database](#database)
5. [Shop layout: Twig events replaced by Twig hooks](#shop-layout-twig-events-replaced-by-twig-hooks)
6. [Admin](#admin)
7. [Namespace changes](#namespace-changes)
8. [Removed classes, services and Twig functions](#removed-classes-services-and-twig-functions)
9. [Rich snippets](#rich-snippets)
10. [Noindex / nofollow filters](#noindex--nofollow-filters)
11. [Custom entities](#custom-entities)
12. [What's new](#whats-new)

---

## Requirements

| | 4.x | 5.0 |
|---|---|---|
| PHP | `^8.0` | `^8.2` |
| Sylius | `~1.11 \|\| ~1.12` | `~2.0.0` |

```bash
composer require dedi/sylius-seo-plugin:^5.0
```

`symfony/webpack-encore-bundle` and `sylius/mailer-bundle` are no longer required by the plugin.

## Configuration and routes

The plugin config import stays the same:

```yaml
# config/packages/dedi_sylius_seo_plugin.yaml
imports:
    - { resource: "@DediSyliusSEOPlugin/Resources/config/config.yaml" }
```

The plugin now ships admin routes (SEO content CRUD). **Import them**, otherwise the admin menu entry and SEO content pages will fail:

```yaml
# config/routes/dedi_sylius_seo_plugin.yaml
dedi_sylius_seo_plugin:
    resource: "@DediSyliusSEOPlugin/Resources/config/routes.yaml"
```

Internal config file changes (only relevant if you imported these files directly):

- `Resources/config/ui.yaml` was removed (Sylius UI events no longer exist in Sylius 2.0). Replaced by `Resources/config/twig_hooks/*.yaml`.
- `Resources/config/resources/content_seo.yaml` was removed. The resource is now declared in `Resources/config/resources.yaml`.
- `Resources/config/admin_routing.yml` was removed. Replaced by `Resources/config/routes.yaml` and `Resources/config/routes/admin_routing.yml`.
- `Resources/config/grid.yaml` was added (SEO content grid).

## Entities

### `ReferenceableInterface` and traits moved and changed

`Domain\SEO\Adapter\ReferenceableInterface` was split in two:

- `Dedi\SyliusSEOPlugin\SEO\Adapter\MetadataAwareInterface`: the metadata getters (`isNotIndexable()`, `getMetadataTitle()`, ...). This is what the old interface used to be.
- `Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface`: extends `MetadataAwareInterface` and adds:
  - `getReferenceableContent(): SEOContentInterface`
  - `setReferenceableContent(?SEOContentInterface $referenceableContent): static`
  - `setReferenceableLocale(string $locale): static`
  - `setReferenceableFallbackLocale(string $locale): static`

`SEOContentInterface` now extends `MetadataAwareInterface` instead of `ReferenceableInterface`.

The Doctrine relation to `SEOContent` is **no longer added automatically** at runtime (see [Removed classes](#removed-classes-services-and-twig-functions)).
Each entity must use a dedicated trait that declares the mapping:

| Entity | Trait to use |
|---|---|
| `Product` | `Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableProductTrait` |
| `Taxon` | `Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTaxonTrait` |
| `Channel` | `Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableChannelTrait` |
| Custom entity | `Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTrait` + your own mapping (see [Custom entities](#custom-entities)) |

`createReferenceableContent()` must now return `SEOContentInterface` instead of `ReferenceableInterface`.

**Before (4.x):**

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

**After (5.0):**

```php
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableProductTrait;
use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct implements ReferenceableInterface
{
    use ReferenceableProductTrait;

    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }
}
```

Apply the same change to `Channel` with `ReferenceableChannelTrait`.

### Taxon is now referenceable

Taxons get their own SEO content (form tab in admin, metadata on taxon pages). To enable it, make your `Taxon` entity implement `ReferenceableInterface` and use `ReferenceableTaxonTrait`:

```php
use Dedi\SyliusSEOPlugin\Entity\SEOContent;
use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface;
use Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTaxonTrait;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;

class Taxon extends BaseTaxon implements ReferenceableInterface
{
    use ReferenceableTaxonTrait;

    protected function createReferenceableContent(): SEOContentInterface
    {
        return new SEOContent();
    }
}
```

### Locale handling is automatic

A Doctrine `postLoad` listener (`ORMReferenceableLocaleListener`) now sets the current and fallback locale on every `ReferenceableInterface` entity.
If you overrode metadata getters to provide default values, remove manual locale juggling such as:

```diff
 public function getMetadataTitle(): ?string
 {
     if (null === $this->getReferenceableContent()->getMetadataTitle()) {
-        $this->setCurrentLocale($this->getReferenceableContent()->getTranslation()->getLocale());
-
         return $this->getName();
     }

     return $this->getBaseMetadataTitle();
 }
```

Likewise, `{% set donothing = resource.referenceableContent.setCurrentLocale(locale) %}` in your templates is no longer needed.

### `SEOContent` changes

- Indexability is now stored **per locale** in a new `SEOContentRobot` entity (`dedi_sylius_seo_content_robots` table), independently of translations. This allows setting indexability for a locale that has no translation.
  - New methods: `getRobots()`, `getRobot()` (robot of the current locale), `addRobot()`, `removeRobot()`.
  - `SEOContent::isNotIndexable()` reads the robot of the current locale, then falls back to the robot of the fallback locale, and defaults to indexable. This keeps the 4.x behaviour, where an untranslated locale inherited the fallback translation's flag.
  - `SEOContent::setNotIndexable()` now writes to the robot of the current locale and creates it if needed. In 4.x it wrote to the translation.
  - `SEOContentTranslation::$notIndexable` is **deprecated** and no longer read.
  - `SEOContentTranslationInterface::isNotIndexable()` / `setNotIndexable()` were removed from the interface.
- `openGraphMetadataType` moved from `SEOContentTranslation` to `SEOContent` (no longer translatable).
  - `SEOContentTranslation::$openGraphMetadataType` is **deprecated** and no longer read.
  - `SEOContentTranslationInterface::getOpenGraphMetadataType()` / `setOpenGraphMetadataType()` were removed from the interface.
- New `type` field (`product`, `taxon`, `channel` or `uri`, see `Dedi\SyliusSEOPlugin\SEO\Enum\MetadataTypeEnum`).
- New `uri` field on translations, used by SEO contents of type `uri` to target any shop URL.
- New inverse relations: `getProduct()` / `setProduct()`, `getTaxon()` / `setTaxon()`, `getChannel()` / `setChannel()`.
- Setters now return `static` instead of `self`. Update the return types if you extend `SEOContent` or `SEOContentTranslation`.

`SEOContent` is now a proper Sylius resource with its own repository (`Dedi\SyliusSEOPlugin\Repository\SEOContentRepository`) and factory (`Dedi\SyliusSEOPlugin\Factory\SEOContentFactory`, method `createTyped(string $type)`).

## Database

The schema changes are:

- New table `dedi_sylius_seo_content_robots` (`seo_content_id`, `locale_code`, `is_not_indexable`), with a unique constraint on (`seo_content_id`, `locale_code`).
- New columns `type` and `og_metadata_type` on `dedi_sylius_seo_content`.
- New column `uri` on `dedi_sylius_seo_content_translation`.
- New column `referenceableContent_id` (+ foreign key, `ON DELETE SET NULL`) on `sylius_taxon`.
- Foreign key on `sylius_product.referenceableContent_id` and `sylius_channel.referenceableContent_id` now uses `ON DELETE SET NULL`.

> **Warning:** In 4.x the join column name came from your Doctrine naming strategy. In 5.0 it is hard-coded to `referenceableContent_id`.
> With the default naming strategy nothing changes. If you use another strategy (e.g. `underscore` → `referenceable_content_id`), the generated migration will drop the old column and create a new one, **losing the link between your entities and their SEO content**.
> Edit the migration to rename the column instead of dropping it.

Then move existing data into the new structure. The plugin ships a data migration that:

- copies `seo_not_indexable` of each translation into `dedi_sylius_seo_content_robots` (one robot per existing translation, same locale),
- fills `dedi_sylius_seo_content.type` according to the entity owning the SEO content,
- copies the Open Graph type from the translations to `dedi_sylius_seo_content.og_metadata_type`. The value from a channel's default locale is used first, then any non-empty translated value. If your translations had different Open Graph types per locale, only one is kept, so review them in the admin afterwards.

It is safe to run more than once: robots, types and Open Graph types that are already set are not overwritten.
Locales without a translation get no robot. They inherit the fallback locale's indexability at runtime, as they did in 4.x.

Steps:

1. Generate and review the schema migration, then run it:

    ```bash
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```

2. Copy the plugin data migration into your project, **after** the schema migration has run (the data migration needs the new table and columns):

    ```bash
    cp vendor/dedi/sylius-seo-plugin/src/Migrations/Version20250114142339.php src/Migrations/
    ```

    Change its namespace to your migrations namespace (e.g. `DoctrineMigrations`).
    Its version is older than the migration you just generated: if you run migrations in a fresh environment, rename the class and file with a newer timestamp so it runs after your schema migration.

3. Run the data migration:

    ```bash
    bin/console doctrine:migrations:migrate
    ```

The deprecated columns `seo_not_indexable` and `seo_og_metadata_type` on `dedi_sylius_seo_content_translation` are still mapped and are kept. Do not drop them yourself.

## Shop layout: Twig events replaced by Twig hooks

All `sylius_template_event('dedi_sylius_seo_plugin.*')` events were removed:

| 4.x event | 5.0 replacement |
|---|---|
| `dedi_sylius_seo_plugin.title` | `dedi_sylius_seo_get_title('Default title')` Twig function |
| `dedi_sylius_seo_plugin.metatags` | Twig hook `sylius_shop.base#metatags` (registered by the plugin) |
| `dedi_sylius_seo_plugin.rich_snippets` | Twig hook `sylius_shop.base#metatags` (registered by the plugin) |
| `dedi_sylius_seo_plugin.links` | Twig hook `sylius_shop.base.head` (registered by the plugin) |
| `sylius.shop.layout.head` (Google tracking) | Twig hook `sylius_shop.base.head` (registered by the plugin) |
| `sylius.shop.layout.before_body` (Google Tag Manager noscript) | Twig hook `sylius_shop.base.before_body` (registered by the plugin) |

The `resource` variable is no longer passed. The plugin finds the current page's metadata itself (see [Metadata contexts](#metadata-contexts)).

1. Delete your `templates/bundles/SyliusShopBundle/layout.html.twig` override made for the plugin.
2. Override `templates/bundles/SyliusShopBundle/shared/layout/base.html.twig` instead. Copy it from Sylius, then:
    - replace the `<title>` with `dedi_sylius_seo_get_title()`. Rename the block to `seo_title` so child templates that redefine `{% block title %}` do not override the SEO title,
    - make sure the `#metatags`, `head` and `before_body` hooks are present.

```twig
{# templates/bundles/SyliusShopBundle/shared/layout/base.html.twig #}
<!DOCTYPE html>

<html lang="{{ app.request.locale|slice(0, 2) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

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
    {% hook 'before_body' with { _prefixes: prefixes } %}
    {# ... rest of the Sylius template #}
```

### Template renames

If you overrode plugin templates, move them to the new paths. Overrides of removed templates must be rewritten.

| 4.x | 5.0 |
|---|---|
| `Shop/Header/_title.html.twig` | removed, use `dedi_sylius_seo_get_title()` |
| `Shop/Header/_metatags.html.twig` | `shop/head/metatags.html.twig` (rewritten, see below) |
| `Shop/Header/_richSnippets.html.twig` | `shop/head/rich_snippets.html.twig` |
| `Shop/Header/_links.html.twig` | `shop/head/links.html.twig` |
| `Shop/Header/_googleTracking.html.twig` | `shop/head/google_tracking.html.twig` |
| `Shop/Body/_googleTracking.html.twig` | `shop/body/google_tracking.html.twig` |
| `DataCollector/rich_snippets.html.twig` | `data_collector/rich_snippets.html.twig` |
| `Admin/Channel/Form/_seo.html.twig` | removed, replaced by Twig hooks |
| `Admin/Product/Tab/_seo.html.twig` | removed, replaced by Twig hooks |
| `Admin/SEO/form_block_type.html.twig` | removed (the form theme is no longer prepended to Twig config) |

`shop/head/metatags.html.twig` now reads a `Metadata` object returned by `dedi_sylius_seo_get_metadata()`, instead of reading `resource` / `sylius.channel` directly:

```twig
{% set metadata = dedi_sylius_seo_get_metadata() %}
{# metadata.indexable, metadata.title, metadata.description, metadata.ogTitle,
   metadata.ogDescription, metadata.ogUrl, metadata.ogType, metadata.ogImage #}
```

Other output changes:

- The `noindex` robots meta now outputs `noindex, nofollow` (it was `noindex` for SEO contents).
- The `noindex, nofollow` meta from noindex filters is rendered by `metatags.html.twig`. It was in `links.html.twig`.
- `hreflang` values now use a dash (`en-us`) instead of an underscore (`en_us`).

## Admin

- SEO forms in admin are rendered with Sylius 2.0 Twig hooks (`Resources/config/twig_hooks/*_admin.yaml`) instead of Sylius UI events and the product form menu.
- `Dedi\SyliusSEOPlugin\Menu\Admin\ProductMenuBuilder` (listener on `sylius.menu.admin.product.form`) was removed.
- New **Marketing > SEO** admin menu entry (`SEOMenuBuilder`) with a grid listing all SEO contents, filterable by type, product, taxon and indexability.
- SEO contents of type `uri` can be created from this page to set metadata for any shop URL.
- Form extensions now target the Sylius 2.0 admin form types (`Sylius\Bundle\AdminBundle\Form\Type\ProductType`, `ChannelType`, `TaxonType`). The `referenceableContent` field is only added when the form data implements `ReferenceableInterface`.
- `SEOContentType` has a new `type` option (`product`, `taxon`, `channel`, `uri`, default `null`). It sets `SEOContent::$type`, so pass it when you add the field to your own form types.
- New form types: `Form\Type\Admin\SEOContentType`, `SEOContentRobotType`, `SEOContentRobotsType`, `IndexableFilterType`.
- New `Dedi\SyliusSEOPlugin\Validation\Constraints\SEOContent` constraint.

## Namespace changes

Replace the old namespaces in your code:

| 4.x | 5.0 |
|---|---|
| `Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableInterface` | `Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableInterface` (see [Entities](#entities)) |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\ReferenceableTrait` | `Dedi\SyliusSEOPlugin\SEO\Adapter\ReferenceableTrait` (prefer the `Referenceable{Product,Taxon,Channel}Trait`) |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\SeoAwareChannelInterface` | `Dedi\SyliusSEOPlugin\SEO\Adapter\SeoAwareChannelInterface` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\SeoAwareChannelTrait` | `Dedi\SyliusSEOPlugin\SEO\Adapter\SeoAwareChannelTrait` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface` | `Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetProductSubjectInterface` | `Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductSubjectInterface` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetProductSubjectTrait` | `Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetProductSubjectTrait` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Factory\AbstractRichSnippetFactory` | `Dedi\SyliusSEOPlugin\RichSnippet\Factory\AbstractRichSnippetFactory` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetFactoryInterface` | `Dedi\SyliusSEOPlugin\RichSnippet\Factory\RichSnippetFactoryInterface` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetSubjectUrlFactory(Interface)` | `Dedi\SyliusSEOPlugin\RichSnippet\Factory\RichSnippetSubjectUrlFactory(Interface)` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Factory\SubjectUrl\SubjectUrlGeneratorInterface` | `Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator\SubjectUrlGeneratorInterface` |
| `Dedi\SyliusSEOPlugin\Factory\SubjectUrl\{Homepage,Product,Taxon}UrlGenerator` | `Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator\{Homepage,Product,Taxon}UrlGenerator` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface` | `Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet\RichSnippetInterface` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippet\*` | `Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet\*` |
| `Dedi\SyliusSEOPlugin\Domain\SEO\Model\Subject\*` | `Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\*` |
| `Dedi\SyliusSEOPlugin\Factory\BreadcrumbRichSnippetFactory` | `Dedi\SyliusSEOPlugin\RichSnippet\Factory\BreadcrumbRichSnippetFactory` |
| `Dedi\SyliusSEOPlugin\Factory\ProductRichSnippetFactory` | `Dedi\SyliusSEOPlugin\RichSnippet\Factory\ProductRichSnippetFactory` |
| `Dedi\SyliusSEOPlugin\Context\RichSnippetContext` | `Dedi\SyliusSEOPlugin\RichSnippet\Context\RichSnippetContext` (+ new `RichSnippetContextInterface`) |
| `Dedi\SyliusSEOPlugin\Context\SubjectFetcher\*` | `Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\*` |
| `Dedi\SyliusSEOPlugin\Twig\RichSnippetsExtension` | `Dedi\SyliusSEOPlugin\RichSnippet\Twig\RichSnippetsExtension` |
| `Dedi\SyliusSEOPlugin\Twig\ReferenceableImageExtension` | `Dedi\SyliusSEOPlugin\SEO\Twig\ReferenceableImageExtension` |
| `Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\NoIndexNoFollowFilterInterface` | `Dedi\SyliusSEOPlugin\Filter\FilterInterface` |
| `Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\FilterRegistry` | `Dedi\SyliusSEOPlugin\Filter\FilterRegistry` (+ new `FilterRegistryInterface`) |
| `Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\Logic\*` | `Dedi\SyliusSEOPlugin\Filter\Logic\*` |
| `Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\Specification\*` | `Dedi\SyliusSEOPlugin\Filter\Specification\*` |

Quick search & replace for most cases:

```bash
grep -rl 'Dedi\\SyliusSEOPlugin\\Domain\\SEO' src/ templates/ config/
```

## Removed classes, services and Twig functions

| Removed | Replacement |
|---|---|
| `Event\DynamicRelationWithReferenceableContentSubscriber` (service `dedi_sylius_seo_plugin.subscriber.dynamic_relation_with_content_seo`) | Explicit Doctrine mapping in `Referenceable{Product,Taxon,Channel}Trait` or in your entity |
| `Twig\NoIndexNoFollowExtension` (service `dedi_sylius_seo_plugin.links.twig.no_index_no_follow_extension`) | `RequestParametersMetadataContext` + `dedi_sylius_seo_get_metadata().indexable` |
| Twig function `dedi_sylius_seo_is_no_index_no_follow()` | `not dedi_sylius_seo_get_metadata().indexable` |
| `Menu\Admin\ProductMenuBuilder` | Admin Twig hooks |
| `DediSyliusSEOExtension::prepend()` (form theme `@DediSyliusSEOPlugin/Admin/SEO/form_block_type.html.twig`) | Admin Twig hooks / Live component `sylius_admin:seo_content:form` |

Renamed services:

| 4.x | 5.0 |
|---|---|
| `dedi_sylius_seo_plugin.links.shop.no_index_no_follow_filter_registry` | `dedi_sylius_seo_plugin.links.shop.filter_registry` |
| `dedi.sylius_seoplugin.context.no_index_no_follow_filter.specification.is_taxon_filter` | `dedi.sylius_seo_plugin.context.filter.specification.is_taxon_filter` |
| `dedi.sylius_seoplugin.context.no_index_no_follow_filter.specification.is_sorting_filter` | `dedi.sylius_seo_plugin.context.filter.specification.is_sorting_filter` |
| `dedi.sylius_seoplugin.context.no_index_no_follow_filter.specification.is_paginated_filter` | `dedi.sylius_seo_plugin.context.filter.specification.is_paginated_filter` |

Twig functions still available: `dedi_sylius_seo_get_rich_snippets()`, `dedi_sylius_seo_get_image_url()`.
New Twig functions: `dedi_sylius_seo_get_title(?string $default)`, `dedi_sylius_seo_get_metadata()`.

## Rich snippets

- `SubjectFetcherInterface::canFromRequest(Request $request)` was renamed to `supports(Request $request)`. Rename it in your custom subject fetchers.
- `RichSnippetInterface` has a new method `addData(array $data): static`. Implement it in your custom rich snippets.
- Service tags are unchanged: `dedi_sylius_seo_plugin.rich_snippets.subject_fetcher`, `dedi_sylius_seo_plugin.rich_snippets.factory`, `dedi_sylius_seo_plugin.rich_snippets.subject_url_generator`.
- Service IDs are unchanged. Only their classes moved (see [Namespace changes](#namespace-changes)).

## Noindex / nofollow filters

- `NoIndexNoFollowFilterInterface` is now `Dedi\SyliusSEOPlugin\Filter\FilterInterface` (same `isSatisfiedBy(Request $request): bool` method).
- The tag `dedi_sylius_seo_plugin.links.shop.no_index_no_follow_filter` and the `_seo.no_index_no_follow_filter` route option are unchanged.
- New specifications available to compose your filters: `IsHomePageFilter`, `IsContactPageFilter`, `IsProductFilter`, `IsCartFilter`, `IsLoginFilter`.

## Custom entities

The Doctrine relation used to be added automatically to any entity implementing `ReferenceableInterface` with a `referenceableContent` property. You must now declare it yourself:

1. Use `ReferenceableTrait` and map the owning side of the relation on `$referenceableContent` (`inversedBy` your custom field on `SEOContent`).
2. Extend `SEOContent` to add the inverse side (`mappedBy="referenceableContent"`), and declare it as the `dedi_sylius_seo_plugin.seo_content` resource model.
3. Pass the `type` option when adding `SEOContentType` to your form.
4. Create a class implementing `Dedi\SyliusSEOPlugin\SEO\Context\MetadataContextInterface` and tag it `dedi_sylius_seo_plugin.context.metadata`, so your entity metadata is rendered on its shop page.

See [doc/SEO_CUSTOM.md](doc/SEO_CUSTOM.md) for a complete example.

## What's new

### Metadata contexts

Shop metadata now comes from a prioritized chain of contexts (tag `dedi_sylius_seo_plugin.context.metadata`). The first one that returns a result wins:

| Priority | Context | Source |
|---|---|---|
| 100 | `UriMetadataContext` | SEO content of type `uri` matching the current URL and locale |
| 50 | `RequestParametersMetadataContext` | noindex filters (`_seo.no_index_no_follow_filter` route option) |
| 0 | `ProductMetadataContext` | current product |
| 0 | `TaxonMetadataContext` | current taxon |
| -50 | `ChannelMetadataContext` | current channel |

Add your own context by implementing `MetadataContextInterface` and tagging the service with a priority. Throw `ContextNotAvailableInRequestException` when the context does not apply.

### API

SEO contents are exposed through API Platform (admin and shop), with filters on `uri`, `type`, product code, taxon code and channel code.
New endpoints return the metadata of a product or taxon (`GetProductMetadataAction`, `GetTaxonMetadataAction`).
