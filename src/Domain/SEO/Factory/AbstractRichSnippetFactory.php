<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;

abstract class AbstractRichSnippetFactory implements RichSnippetFactoryInterface
{
    final public function can(RichSnippetSubjectInterface $subject): bool
    {
        return in_array($subject->getRichSnippetSubjectType(), $this->getHandledSubjectTypes());
    }

    /**
     * @return string[] handled subject types for this factory
     */
    abstract protected function getHandledSubjectTypes(): array;
}
