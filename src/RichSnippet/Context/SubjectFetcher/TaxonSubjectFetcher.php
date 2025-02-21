<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class TaxonSubjectFetcher implements SubjectFetcherInterface
{
    /** @param TaxonRepositoryInterface<TaxonInterface> $repository */
    public function __construct(
        private readonly FilterInterface $filter,
        private readonly LocaleContextInterface $localeContext,
        private readonly TaxonRepositoryInterface $repository,
    ) {
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
