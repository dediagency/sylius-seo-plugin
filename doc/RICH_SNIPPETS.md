# Rich Snippets

This plugin allow you to add RichSnippet on your pages.

## How does it work ?

The RichSnippetController is the entrypoint where RichSnippets are generated.

1. Depending on the provided parameters (Subject Type and Id), it will first try to find the right `RichSnipetSubjectInterface $subject`.
2. Then it will get the right factory for the provided Rich Snippet Type.
3. This factory will then be used to generate a `RichSnippetInterface $richSnippet`.
4. Held data in the newly created `$richSnippet` will be rendered in the `JSonReponse` sent by the controller. 

In order to be easily extensible, several `Chain of Responsibility` design pattern have been implemented.

| Class | Method |
| ----- | ------ |
| RichSnippetContext | getAvailableRichSnippets() |
| RichSnippetContext | guessSubject() |
| RichSnippetSubjectUrlFactory | buildUrl() |

## Creating a new Subject type

In order to create a new subject, you need to create a new service implementing the interface `SubjectFetcherInterface` and is tagged `̀dedi_sylius_seo_plugin.rich_snippets.subject_fetcher.

Example:

```php
<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\SubjectFetcher;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\GenericPageRichSnippetSubject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactSubjectFetcher implements SubjectFetcherInterface
{
    public const TYPE = 'contact';

    private HomepageSubjectFetcher $homepageSubjectFetcher;
    private TranslatorInterface $translator;

    // ...

    public function can(string $type, ?int $id): bool
    {
        return self::TYPE === $type && null === $id;
    }

    public function fetch(string $type, ?int $id = null): ?RichSnippetSubjectInterface
    {
        return new GenericPageRichSnippetSubject(
            $this->translator->trans('sylius.ui.contact_us'),
            self::TYPE,
            $this->homepageSubjectFetcher->fetch(HomepageSubjectFetcher::TYPE)
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

In order to create a new RichSnippetFactory, you should implement the interface `RichSnippetFactoryInterface` and tag your new service with dedi_sylius_seo_plugin.rich_snippets.factory`.

Example:

```php
<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\HomepageSubjectFetcher;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetFactoryInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetSubjectUrlFactory;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\BreadcrumbRichSnippet;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\HomepageRichSnippetSubject;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;

final class BreadcrumbRichSnippetFactory implements RichSnippetFactoryInterface
{
    public const TYPE = 'breadcrumb';

    private RichSnippetSubjectUrlFactory $richSnippetSubjectUrlFactory;
    private HomepageSubjectFetcher $homepageSubjectFetcher;

    public function __construct(
        RichSnippetSubjectUrlFactory $richSnippetSubjectUrlFactory,
        HomepageSubjectFetcher $homepageSubjectFetcher
    ) {
        $this->richSnippetSubjectUrlFactory = $richSnippetSubjectUrlFactory;
        $this->homepageSubjectFetcher = $homepageSubjectFetcher;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function can(string $type, RichSnippetSubjectInterface $subject): bool
    {
        return self::TYPE === $type;
    }

    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface
    {
        return $this->build($subject, new BreadcrumbRichSnippet(), true);
    }

    private function build(
        RichSnippetSubjectInterface $subject,
        BreadcrumbRichSnippet $richSnippet,
        bool $isLeaf = false
    ): BreadcrumbRichSnippet {
        // ...
    }
}
```
