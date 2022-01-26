<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context;

use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\SubjectFetcherInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RichSnippetContext
{
    private ?Request $request;
    /** @var SubjectFetcherInterface[] */
    private array $subjectFetchers;
    /** @var RichSnippetFactoryInterface[] */
    private array $richSnippetFactories;

    public function __construct(
        RequestStack $requestStack,
        \Traversable $subjectFetchers,
        \Traversable $richSnippetFactories
    ) {
        $this->request = $requestStack->getMasterRequest();
        $this->subjectFetchers = iterator_to_array($subjectFetchers);
        $this->richSnippetFactories = iterator_to_array($richSnippetFactories);
    }

    /**
     * Iterates over RichSnippetFactoryInterface[] in order to get every Rich Snippets Urls available for a given subject.
     *
     * @return array
     */
    public function getAvailableRichSnippets(): array
    {
        $subject = $this->guessSubject();
        if (null === $subject) {
            return [];
        }

        $richSnippets = [];

        foreach ($this->richSnippetFactories as $factory) {
            if ($factory->can($subject)) {
                $richSnippets[] = $factory->buildRichSnippet($subject);
            }
        }

        return $richSnippets;
    }

    /**
     * Iterates over SubjectFetcherInterface[] in order to find a subject for the current request.
     *
     * @return RichSnippetSubjectInterface|null
     */
    private function guessSubject(): ?RichSnippetSubjectInterface
    {
        if (null === $this->request) {
            return null;
        }

        foreach ($this->subjectFetchers as $subjectFetcher) {
            if ($subjectFetcher->canFromRequest($this->request)) {
                return $subjectFetcher->fetchFromRequest($this->request);
            }
        }

        return null;
    }
}
