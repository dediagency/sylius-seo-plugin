<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Adapter;

interface RichSnippetSubjectInterface
{
    /**
     * @return int|null
     */
    public function getId();

    public function getRichSnippetSubjectType(): string;

    public function getName(): ?string;

    public function getRichSnippetSubjectParent(): ?self;
}
