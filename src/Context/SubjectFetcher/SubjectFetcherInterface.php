<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\SubjectFetcher;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Symfony\Component\HttpFoundation\Request;

interface SubjectFetcherInterface
{
    public function can(string $type, ?int $id): bool;

    public function fetch(string $type, ?int $id): ?RichSnippetSubjectInterface;

    public function canFromRequest(Request $request): bool;

    public function fetchFromRequest(Request $request): ?RichSnippetSubjectInterface;
}
