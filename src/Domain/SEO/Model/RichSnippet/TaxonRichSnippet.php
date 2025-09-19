<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippet;

use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;

final class TaxonRichSnippet implements RichSnippetInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function addData(array $data): self
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function getData(): array
    {
        return [
            array_merge([
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
            ], $this->data),
        ];
    }

    public function getType(): string
    {
        return 'taxon';
    }
}
