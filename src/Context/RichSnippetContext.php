<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Context\SubjectFetcher\SubjectFetcherInterface;
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
                    'subjectType' => $subject->getRichSnippetType(),
                    'id' => $subject->getId(),
                ];
            }
        }

        return $urls;
    }

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
