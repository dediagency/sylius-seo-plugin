# Rich Snippets

This plugin renders RichSnippets (JSON-LD) on shop pages. This page explains how it works and how to add your own.

## How does it work ?

`RichSnippetContext::getAvailableRichSnippets()` is the entrypoint where RichSnippets are generated.

1. The context finds the **subject** of the current page (a `RichSnippetSubjectInterface`: a product, a taxon, the homepage, ...). It iterates over the subject fetchers (`SubjectFetcherInterface`) and uses the first one that `supports()` the request.
2. It asks every **factory** (`RichSnippetFactoryInterface`) whether it `can()` build a rich snippet for this subject, and builds one with each factory that can.
3. The `dedi_sylius_seo_get_rich_snippets()` Twig function returns these rich snippets. `@DediSyliusSEOPlugin/shop/head/rich_snippets.html.twig` renders them in the `sylius_shop.base#metatags` hook.

When a factory needs the URL of a subject (e.g. for the breadcrumb), it calls `RichSnippetSubjectUrlFactory::buildUrl()`, which uses the first URL generator (`SubjectUrlGeneratorInterface`) that `can()` handle the subject.

Each extension point is a tagged service:

| Interface | Service tag | Provided |
|---|---|---|
| `RichSnippet\Context\SubjectFetcher\SubjectFetcherInterface` | `dedi_sylius_seo_plugin.rich_snippets.subject_fetcher` | product, taxon, homepage, contact |
| `RichSnippet\Factory\RichSnippetFactoryInterface` | `dedi_sylius_seo_plugin.rich_snippets.factory` | breadcrumb, product |
| `RichSnippet\UrlGenerator\SubjectUrlGeneratorInterface` | `dedi_sylius_seo_plugin.rich_snippets.subject_url_generator` | homepage, product, taxon |

## Subjects

A subject implements `Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface`:

```php
public function getId();
public function getRichSnippetSubjectType(): string; // e.g. 'product', 'taxon'
public function getName(): ?string;
public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface; // used by the breadcrumb
```

Your entities can implement it directly (see [installation](INSTALL.md#rich-snippet-usage-for-product-and-taxon-entities)). For pages without an entity, use `Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\GenericPageRichSnippetSubject`.

## Creating a new Subject fetcher

Create a service implementing `SubjectFetcherInterface` and tag it `dedi_sylius_seo_plugin.rich_snippets.subject_fetcher`.

Example, the contact page fetcher provided by the plugin:

```php
<?php

declare(strict_types=1);

namespace App\RichSnippet\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher\SubjectFetcherInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\GenericPageRichSnippetSubject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactSubjectFetcher implements SubjectFetcherInterface
{
    public const TYPE = 'contact';

    public function __construct(
        private readonly FilterInterface $filter, // IsContactPageFilter
        private readonly SubjectFetcherInterface $homepageSubjectFetcher,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function fetch(?int $id = null): ?RichSnippetSubjectInterface
    {
        $subject = $this->homepageSubjectFetcher->fetch();

        if (null === $subject) {
            return null;
        }

        // the homepage is the parent of the contact page in the breadcrumb
        return new GenericPageRichSnippetSubject(
            $this->translator->trans('sylius.ui.contact_us'),
            self::TYPE,
            $subject,
        );
    }

    public function supports(Request $request): bool
    {
        return $this->filter->isSatisfiedBy($request);
    }

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface
    {
        return $this->fetch();
    }
}
```

```yaml
# config/services.yaml
services:
    app.rich_snippets.subject_fetcher.contact:
        class: App\RichSnippet\SubjectFetcher\ContactSubjectFetcher
        arguments:
            - '@dedi.sylius_seo_plugin.context.filter.specification.is_contact_filter'
            - '@dedi_sylius_seo_plugin.rich_snippets.context.subject_fetcher.homepage_subject_fetcher'
            - '@translator'
        tags:
            - { name: dedi_sylius_seo_plugin.rich_snippets.subject_fetcher }
```

## Creating a new RichSnippetFactory

Extend `AbstractRichSnippetFactory`, list the subject types it handles, and tag the service `dedi_sylius_seo_plugin.rich_snippets.factory`.

The rich snippet itself implements `RichSnippetInterface` (`addData()`, `getData()`, `getType()`). `getData()` returns the list of JSON-LD objects to render.

Example, an `Organization` rich snippet on the homepage:

```php
<?php

declare(strict_types=1);

namespace App\RichSnippet;

use Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet\RichSnippetInterface;

final class OrganizationRichSnippet implements RichSnippetInterface
{
    public function __construct(private array $data = [])
    {
    }

    public function addData(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function getData(): array
    {
        return [
            array_merge([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
            ], $this->data),
        ];
    }

    public function getType(): string
    {
        return 'organization';
    }
}
```

```php
<?php

declare(strict_types=1);

namespace App\RichSnippet;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Factory\AbstractRichSnippetFactory;
use Dedi\SyliusSEOPlugin\RichSnippet\Factory\RichSnippetSubjectUrlFactoryInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet\RichSnippetInterface;

final class OrganizationRichSnippetFactory extends AbstractRichSnippetFactory
{
    public function __construct(
        private readonly RichSnippetSubjectUrlFactoryInterface $urlFactory,
    ) {
    }

    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface
    {
        return new OrganizationRichSnippet([
            'name' => $subject->getName(),
            'url' => $this->urlFactory->buildUrl($subject),
        ]);
    }

    protected function getHandledSubjectTypes(): array
    {
        return ['homepage'];
    }
}
```

```yaml
# config/services.yaml
services:
    app.rich_snippets.factory.organization:
        class: App\RichSnippet\OrganizationRichSnippetFactory
        arguments:
            - '@dedi_sylius_seo_plugin.rich_snippets.factory.subject_url'
        tags:
            - { name: dedi_sylius_seo_plugin.rich_snippets.factory }
```

Alternatively, your class can implement `RichSnippetFactoryInterface` directly to have more control over which subjects your factory can handle.

## Extending a provided factory

Factories are regular services. To add data to the product rich snippet, decorate or extend `dedi_sylius_seo_plugin.rich_snippets.factory.product` (`ProductRichSnippetFactory`). Its building methods are `protected` (`buildProductGroupRichSnippet()`, `buildVariant()`, `buildOffer()`, `getIdentifiers()`, `getVariantName()`, `getVariantUrl()`, ...), so a subclass can change any part of the generated data. For example, override `buildOffer()` to add `itemCondition` or shipping details.

## Debug

Rich snippets of the current page are listed in the Symfony Profiler (see [Features](FEATURES.md#debug)).
