<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\GenericPageRichSnippetSubject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactSubjectFetcher implements SubjectFetcherInterface
{
    public const TYPE = 'contact';

    public function __construct(
        private readonly FilterInterface $filter,
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
