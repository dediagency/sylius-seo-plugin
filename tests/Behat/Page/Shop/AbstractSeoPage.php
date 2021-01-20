<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

abstract class AbstractSeoPage extends SymfonyPage implements SeoPage
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

    public function hasLinkRelCanonical(): bool
    {
        return null !== $this->getDocument()->find('css', 'link[rel="canonical"]');
    }

    public function getLinkRelCanonical(): string
    {
        return $this->getDocument()->find('css', 'link[rel="canonical"]')->getAttribute('href');
    }

    public function hasLinkAlternateForLocale(string $localeCode): bool
    {
        return null !== $this->getDocument()->find('css', sprintf('link[rel="alternate"][hreflang="%s"]', strtolower($localeCode)));
    }

    public function getLinkRelAlternateForLocale(string $localeCode): string
    {
        return $this->getDocument()->find('css', sprintf('link[rel="alternate"][hreflang="%s"]', strtolower($localeCode)))->getAttribute('href');
    }

    public function hasNoIndexNoFollowTag(): bool
    {
        return null !== $this->getDocument()->find('css', 'meta[name="robots"][content="noindex, nofollow"]');
    }
}
