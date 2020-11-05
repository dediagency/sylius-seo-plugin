<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

abstract class AbstractRichSnippetAwarePage extends SymfonyPage implements RichSnippetAwarePageInterface
{
    public function getRichSnippetData(): array
    {
        $elements = $this->getDocument()->findAll('css', 'head script[type="application/ld+json"]');

        $richSnippets = [];
        foreach ($elements as $element) {
            $html = $element->getHtml();

            $data = json_decode($html, true);
            $name = $data[0]['@type'];

            $richSnippets[$name] = $data;
        }

        return $richSnippets;
    }

    public function getOgData(): array
    {
        $elements = $this->getDocument()->findAll('css', 'head meta[property^="og:"]');

        $data = [];
        foreach ($elements as $element) {
            $data[$element->getAttribute('property')] = $element->getAttribute('content');
        }

        return $data;
    }
}
