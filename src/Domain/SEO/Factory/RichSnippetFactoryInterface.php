<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;

interface RichSnippetFactoryInterface
{
    /**
     * Can the factory build a RichSnippet for a given type and Subject.
     *
     * @param RichSnippetSubjectInterface $subject
     *
     * @return bool
     */
    public function can(RichSnippetSubjectInterface $subject): bool;

    /**
     * Build a RichSnippet for a given $subject.
     *
     * @param RichSnippetSubjectInterface $subject
     *
     * @return RichSnippetInterface
     */
    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface;
}
