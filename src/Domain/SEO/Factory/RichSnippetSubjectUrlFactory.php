<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Factory\SubjectUrl\SubjectUrlGeneratorInterface;

final class RichSnippetSubjectUrlFactory implements RichSnippetSubjectUrlFactoryInterface
{
    /** @var SubjectUrlGeneratorInterface[] */
    private array $urlGenerators;

    public function __construct(iterable $urlGenerators)
    {
        $this->urlGenerators = iterator_to_array($urlGenerators);
    }

    /**
     * Iterates over SubjectUrlGeneratorInterface[] in order to generate the url to a given subject.
     */
    public function buildUrl(RichSnippetSubjectInterface $subject): string
    {
        foreach ($this->urlGenerators as $generator) {
            if ($generator->can($subject)) {
                return $generator->generateUrl($subject);
            }
        }

        throw new \LogicException(sprintf('Can\'t generate route for Subject with type %s', $subject::class), 400);
    }
}
