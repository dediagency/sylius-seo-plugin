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
        TaxonRepositoryInterface $repository
    ) {
        $this->repository = $repository;
        $this->localeContext = $localeContext;
    }

    public function fetch(?int $id): ?RichSnippetSubjectInterface
    {
        return $this->repository->find($id);
    }

    public function canFromRequest(Request $request): bool
    {
        return 'sylius_shop_product_index' === $request->attributes->get('_route');
    }

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface
    {
        return $this->repository->findOneBySlug(
            $request->attributes->get('slug'),
            $this->localeContext->getLocaleCode()
        );
    }
}
