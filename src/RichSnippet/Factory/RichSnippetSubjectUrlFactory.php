<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Factory;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\UrlGenerator\SubjectUrlGeneratorInterface;
use LogicException;

final class RichSnippetSubjectUrlFactory implements RichSnippetSubjectUrlFactoryInterface
{
    /** @var SubjectUrlGeneratorInterface[] */
    private array $urlGenerators;

    public function __construct(\Traversable $urlGenerators)
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

        throw new LogicException(sprintf("Can't generate route for Subject with type %s", $subject::class));
    }
}
