<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet;

interface RichSnippetInterface
{
    public function addData(array $data): static;

    public function getData(): array;

    public function getType(): string;
}
