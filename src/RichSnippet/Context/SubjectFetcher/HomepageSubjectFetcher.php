<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\Subject\HomepageRichSnippetSubject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomepageSubjectFetcher implements SubjectFetcherInterface
{
    public function __construct(
        private readonly FilterInterface $filter,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function fetch(?int $id = null): ?RichSnippetSubjectInterface
    {
        return new HomepageRichSnippetSubject($this->translator->trans('sylius.ui.home'));
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
