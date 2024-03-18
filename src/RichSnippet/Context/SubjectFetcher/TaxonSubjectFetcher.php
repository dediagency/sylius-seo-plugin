<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class TaxonSubjectFetcher implements SubjectFetcherInterface
{
    private LocaleContextInterface $localeContext;

    private TaxonRepositoryInterface $repository;

    private FilterInterface $filter;

    public function __construct(
        FilterInterface $filter,
        LocaleContextInterface $localeContext,
        TaxonRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
        $this->localeContext = $localeContext;
        $this->filter = $filter;
    }

    public function fetch(?int $id = null): ?RichSnippetSubjectInterface
    {
        if (null === $id) {
            return null;
        }

        /** @var ?RichSnippetSubjectInterface $richSnippetSubject */
        $richSnippetSubject = $this->repository->find($id);

        return $richSnippetSubject;
    }

    public function supports(Request $request): bool
    {
        return $this->filter->isSatisfiedBy($request);
    }

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface
    {
        $slug = $request->attributes->get('slug');
        if (null === $slug) {
            return null;
        }
        Assert::string($slug);

        /** @var RichSnippetSubjectInterface|null $subject */
        $subject = $this->repository->findOneBySlug(
            $slug,
            $this->localeContext->getLocaleCode(),
        );

        return $subject;
    }
}
