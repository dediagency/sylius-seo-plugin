<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\RichSnippet\Model\RichSnippet;

class BreadcrumbRichSnippet implements RichSnippetInterface
{
    private array $elements = [];

    public function addData(array $data): static
    {
        $element = [
            '@type' => 'ListItem',
            'position' => count($this->elements) + 1,
            'name' => $data['name'],
        ];

        if (null !== $data['item']) {
            $element['item'] = $data['item'];
        }

        $this->elements[] = $element;

        return $this;
    }

    public function getData(): array
    {
        return [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $this->elements,
            ],
        ];
    }

    public function getType(): string
    {
        return 'breadcrumb';
    }
}
