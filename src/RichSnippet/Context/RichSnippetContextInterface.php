<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Context;

interface RichSnippetContextInterface
{
    public function getAvailableRichSnippets(): array;
}
