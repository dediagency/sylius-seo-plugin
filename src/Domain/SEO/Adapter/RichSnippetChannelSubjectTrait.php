<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Adapter;

trait RichSnippetChannelSubjectTrait
{
    public function getRichSnippetSubjectType(): string
    {
        return 'channel';
    }

    public function getRichSnippetSubjectParent(): ?RichSnippetSubjectInterface
    {
        return null;
    }
}
