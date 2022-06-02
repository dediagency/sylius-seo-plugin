<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\DataCollector;

use Dedi\SyliusSEOPlugin\Context\RichSnippetContext;
use Dedi\SyliusSEOPlugin\Domain\SEO\Model\RichSnippetInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

class RichSnippetsDataCollector extends DataCollector
{
    private RichSnippetContext $richSnippetContext;

    public function __construct(RichSnippetContext $richSnippetContext)
    {
        $this->richSnippetContext = $richSnippetContext;
    }

    public function collect(Request $request, Response $response, Throwable $exception = null)
    {
        $this->data['rich_snippets'] = $this->richSnippetContext->getAvailableRichSnippets();
        $this->data['html'] = $response->getContent();
    }

    public function getName()
    {
        return 'dedi_sylius_seo_plugin.rich_snippets';
    }

    public function reset()
    {
        $this->data = [];
    }

    /**
     * @return RichSnippetInterface[]
     */
    public function getRichSnippets(): array
    {
        return $this->data['rich_snippets'];
    }

    public function getHtml(): string
    {
        return $this->data['html'];
    }
}
