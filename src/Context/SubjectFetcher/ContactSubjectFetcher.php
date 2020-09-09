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

    public function __construct(HomepageSubjectFetcher $homepageSubjectFetcher, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->homepageSubjectFetcher = $homepageSubjectFetcher;
    }

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
