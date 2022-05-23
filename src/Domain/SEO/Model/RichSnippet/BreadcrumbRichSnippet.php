<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippet;

use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;

class BreadcrumbRichSnippet implements RichSnippetInterface
{
    private array $elements = [];

    public function addElement(string $name, ?string $item = null): self
    {
        $element = [
            '@type' => 'ListItem',
            'position' => count($this->elements) + 1,
            'name' => $name,
        ];

        if (null !== $item) {
            $element['item'] = $item;
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
