<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context;

use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\SubjectFetcherInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\RichSnippetFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

final class RichSnippetContext
{
    private ?Request $request;
    private RouterInterface $router;
    /** @var SubjectFetcherInterface[] */
    private array $subjectFetchers;
    /** @var RichSnippetFactoryInterface[] */
    private array $richSnippetFactories;

    public function __construct(
        RequestStack $requestStack,
        RouterInterface $router,
        iterable $subjectFetchers,
        iterable $richSnippetFactories
    ) {
        $this->request = $requestStack->getMasterRequest();
        $this->router = $router;
        $this->subjectFetchers = iterator_to_array($subjectFetchers);
        $this->richSnippetFactories = iterator_to_array($richSnippetFactories);
    }

    /**
     * Iterates over SubjectFetcherInterface[] in order to find a subject depending on $type.
     *
     * @param string $type
     * @param int|null $id
     *
     * @return RichSnippetSubjectInterface
     *
     * @throws SubjectNotFoundException
     */
    public function getSubject(string $type, ?int $id = null): RichSnippetSubjectInterface
    {
        foreach ($this->subjectFetchers as $subjectFetcher) {
            if ($subjectFetcher->can($type, $id)) {
                $subject = $subjectFetcher->fetch($type, $id);

                if ($subject) {
                    return $subject;
                }
            }
        }

        if ($id) {
            throw new SubjectNotFoundException(sprintf('Subject not found for type: "%s" and id: "%d"', $type, $id));
        }

        throw new SubjectNotFoundException(sprintf('Subject not found for type: "%s"', $type));
    }

    /**
     * Iterates over RichSnippetFactoryInterface[] in order to get every Rich Snippets Urls available for a given subject.
     *
     * @return array
     */
    public function getAvailableRichSnippetsUrls(): array
    {
        $subject = $this->guessSubject();
        if (!$subject) {
            return [];
        }

        $urls = [];

        foreach ($this->richSnippetFactories as $factory) {
            if ($factory->can($factory->getType(), $subject)) {
                $urls[] = [
                    'richSnippetType' => $factory->getType(),
                    'subjectType' => $subject->getRichSnippetSubjectType(),
                    'id' => $subject->getId(),
                ];
            }
        }

        return $urls;
    }

    /**
     * Iterates over SubjectFetcherInterface[] in order to find a subject for the current request.
     *
     * @return RichSnippetSubjectInterface|null
     */
    private function guessSubject(): ?RichSnippetSubjectInterface
    {
        foreach ($this->subjectFetchers as $subjectFetcher) {
            if ($subjectFetcher->canFromRequest($this->request)) {
                return $subjectFetcher->fetchFromRequest($this->request);
            }
        }

        return null;
    }
}
