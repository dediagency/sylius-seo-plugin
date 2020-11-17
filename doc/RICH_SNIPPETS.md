# Rich Snippets

This plugin allow you to add RichSnippet on your pages.

## How does it work ?

The `RichSnippetContext::getAvailableRichSnippets()` is the entrypoint where RichSnippets are generated.

First, the Context will try to find a `RichSnippetSubjectInterface` for the given request. In order to do this, it iterates over `SubjectFetcherInterface`s (see `RichSnippetContext::guessSubject()`).

Then the available `RichSnippetInterface`s for the given subject are created. This is done by iterating over `RichSnippetFactoryInterface`s. These factories are able to tell by themself whether they can handle the given subject.

Finally, `RichSnippetInterface` are returned by the `RichSnippetContext::getAvailableRichSnippets()`.

The twig function `dedi_sylius_seo_get_rich_snippets` is used in `_richSnippets.html` to generate the `RichSnippetInterface`s and render them.

In order to be easily extensible, several `Chain of Responsibility` design pattern have been implemented.

| Class | Method |
| ----- | ------ |
| RichSnippetContext | getAvailableRichSnippets() |
| RichSnippetContext | guessSubject() |
| RichSnippetSubjectUrlFactory | buildUrl() |

## Creating a new Subject type

In order to create a new subject, you need to create a new service implementing the interface `SubjectFetcherInterface` and it must be tagged `dedi_sylius_seo_plugin.rich_snippets.subject_fetcher`.

Example:

```php
<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\SubjectFetcher;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\Subject\GenericPageRichSnippetSubject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactSubjectFetcher implements SubjectFetcherInterface
{
    public const TYPE = 'contact';

    private HomepageSubjectFetcher $homepageSubjectFetcher;
    private TranslatorInterface $translator;

    // ...

    public function fetch(string $type, ?int $id = null): ?RichSnippetSubjectInterface
    {
        return new GenericPageRichSnippetSubject(
            $this->translator->trans('sylius.ui.contact_us'),
            self::TYPE,
            $this->homepageSubjectFetcher->fetch()
        );
    }

    public function canFromRequest(Request $request): bool
    {
        return 'sylius_shop_contact_request' === $request->attributes->get('_route');
    }

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface
    {
        return $this->fetch(self::TYPE);
    }
}
``` 

For more information, please see `Dedi\SyliusSEOPlugin\Context\SubjectFetcher\SubjectFetcherInterface`.

## Creating a new RichSnippetFactory

In order to create a new RichSnippetFactory, you should extend the class `AbstractRichSnippetFactory` and tag your new service with dedi_sylius_seo_plugin.rich_snippets.factory`.

Example:

```php
<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\HomepageSubjectFetcher;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\AbstractRichSnippetFactory;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetSubjectUrlFactory;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippet\BreadcrumbRichSnippet;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;

final class BreadcrumbRichSnippetFactory extends AbstractRichSnippetFactory
{
    private RichSnippetSubjectUrlFactory $richSnippetSubjectUrlFactory;
    private HomepageSubjectFetcher $homepageSubjectFetcher;

    public function __construct(
        RichSnippetSubjectUrlFactory $richSnippetSubjectUrlFactory,
        HomepageSubjectFetcher $homepageSubjectFetcher
    ) {
        $this->richSnippetSubjectUrlFactory = $richSnippetSubjectUrlFactory;
        $this->homepageSubjectFetcher = $homepageSubjectFetcher;
    }

    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface
    {
        //
    }

    protected function getHandledSubjectTypes(): array
    {
        return [
            'homepage',
            'contact',
            'taxon',
            'product',
        ];
    }
}
```

Alternatively, your class can implement `RichSnippetFactoryInterface` in order to have more control over which subject you factory can handle.
