<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet;

final class ProductRichSnippet implements RichSnippetInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function addData(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function getData(): array
    {
        return [
            array_merge([
                '@context' => 'https://schema.org',
                '@type' => 'Product',
            ], $this->data),
        ];
    }

    public function getType(): string
    {
        return 'product';
    }
}
