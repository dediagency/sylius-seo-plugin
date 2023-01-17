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

    private SubjectFetcherInterface $homepageSubjectFetcher;

    private TranslatorInterface $translator;

    public function __construct(SubjectFetcherInterface $homepageSubjectFetcher, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->homepageSubjectFetcher = $homepageSubjectFetcher;
    }

    public function fetch(?int $id = null): ?RichSnippetSubjectInterface
    {
        return new GenericPageRichSnippetSubject(
            $this->translator->trans('sylius.ui.contact_us'),
            self::TYPE,
            $this->homepageSubjectFetcher->fetch(),
        );
    }

    public function canFromRequest(Request $request): bool
    {
        return 'sylius_shop_contact_request' === $request->attributes->get('_route');
    }

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface
    {
        return $this->fetch();
    }
}
