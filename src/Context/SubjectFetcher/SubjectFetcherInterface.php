<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This is interface is used for retrieving a subject depending on a type + id or a request.
 *
 * It can be used to fetch a Subject from the database (for instance for a product), or to create it from scratch (useful for the homepage).
 *
 * Services implementing this interface, should be tagged with `̀dedi_sylius_seo_plugin.rich_snippets.subject_fetcher`
 *
 * Interface SubjectFetcherInterface
 */
interface SubjectFetcherInterface
{
    /**
     * Fetch the Subject for a given id.
     */
    public function fetch(?int $id = null): ?RichSnippetSubjectInterface;

    /**
     * Define if the fetcher can fetch a Subject for a given request.
     * You may want to test the current route here.
     */
    public function canFromRequest(Request $request): bool;

    /**
     * Fetch the Subject for a given request.
     * You may want to use the slug / id in the stored in the request parameters.
     */
    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface;
}
