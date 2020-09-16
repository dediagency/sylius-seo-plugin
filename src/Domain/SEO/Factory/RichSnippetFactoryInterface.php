<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;

interface RichSnippetFactoryInterface
{
    /**
     * Return the type of RichSnippet this factory can build.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Can the factory build a RichSnippet for a given type and Subject.
     *
     * @todo : find a **easy to use** way to filter which subject can be used for a factory. In other word, a HomepageSubject should not be used by a ProductRichSnippet Factory.
     *
     * @param string $type
     * @param RichSnippetSubjectInterface $subject
     *
     * @return bool
     */
    public function can(string $type, RichSnippetSubjectInterface $subject): bool;

    /**
     * Build a RichSnippet for a given $subject.
     *
     * @param RichSnippetSubjectInterface $subject
     *
     * @return RichSnippetInterface
     */
    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface;
}
