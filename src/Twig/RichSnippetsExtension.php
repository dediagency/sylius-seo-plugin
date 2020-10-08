<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Twig;

use Dedi\SyliusSEOPlugin\Context\RichSnippetContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RichSnippetsExtension extends AbstractExtension
{
    private RichSnippetContext $richSnippetContext;

    public function __construct(RichSnippetContext $richSnippetContext)
    {
        $this->richSnippetContext = $richSnippetContext;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('dedi_sylius_seo_get_rich_snippets', [$this->richSnippetContext, 'getAvailableRichSnippets']),
        ];
    }
}
