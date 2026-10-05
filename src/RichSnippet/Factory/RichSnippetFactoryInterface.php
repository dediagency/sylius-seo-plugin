<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Factory;

use Dedi\SyliusSEOPlugin\RichSnippet\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet\RichSnippetInterface;

interface RichSnippetFactoryInterface
{
    /**
     * Can the factory build a RichSnippet for a given type and Subject.
     */
    public function can(RichSnippetSubjectInterface $subject): bool;

    /**
     * Build a RichSnippet for a given $subject.
     */
    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface;
}
