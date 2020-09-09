<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Factory;

use Dedi\SyliusSEOPlugin\Domain\SEO\Adapter\RichSnippetSubjectInterface;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;

interface RichSnippetFactoryInterface
{
    public function getType(): string;

    public function can(string $type, RichSnippetSubjectInterface $subject): bool;

    public function buildRichSnippet(RichSnippetSubjectInterface $subject): RichSnippetInterface;
}
