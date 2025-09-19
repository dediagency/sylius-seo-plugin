<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class TaxonSubjectFetcher implements SubjectFetcherInterface
{
    private LocaleContextInterface $localeContext;

    private TaxonRepositoryInterface $repository;

    public function __construct(
        LocaleContextInterface $localeContext,
        TaxonRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
        $this->localeContext = $localeContext;
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

    public function canFromRequest(Request $request): bool
    {
        return 'sylius_shop_product_index' === $request->attributes->get('_route');
    }

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface
    {
        $slug = $request->attributes->get('slug');

        if (null === $slug) {
            return null;
        }

        /** @var RichSnippetSubjectInterface|null $subject */
        $subject = $this->repository->findOneBySlug(
            $slug,
            $this->localeContext->getLocaleCode(),
        );

        return $subject;
    }
}
