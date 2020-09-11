<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;

final class RichSnippetFactoryChain
{
    /** @var RichSnippetFactoryInterface[] */
    private array $factories;

    public function __construct(iterable $factories)
    {
        $this->factories = iterator_to_array($factories);
    }

    /**
     * Iterates over RichSnippetFactoryInterface[] in order to find a factory for the given $type and $subject
     *
     * @param string $type
     * @param RichSnippetSubjectInterface $subject
     *
     * @return RichSnippetFactoryInterface|mixed
     *
     * @throws RichSnippetFactoryNotFoundException
     */
    public function getRichSnippetFactory(string $type, RichSnippetSubjectInterface $subject)
    {
        foreach ($this->factories as $factory) {
            if ($factory->can($type, $subject)) {
                return $factory;
            }
        }

        throw new RichSnippetFactoryNotFoundException(sprintf('Rich snippet not found for type "%s"', $type));
    }
}
