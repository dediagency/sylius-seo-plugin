<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop;

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

abstract class AbstractSeoPage extends SymfonyPage implements SeoPage
{
    public function getRichSnippetData(): array
    {
        $elements = $this->getDocument()->findAll('css', 'script[type="application/ld+json"]');

        $richSnippets = [];
        foreach ($elements as $element) {
            $html = $element->getHtml();

            $data = json_decode($html, true);
            if (!is_array($data) || !isset($data[0]) || !is_array($data[0]) || !is_string($data[0]['@type'] ?? null)) {
                continue;
            }
            $name = $data[0]['@type'];

            $richSnippets[$name] = $data;
        }

        return $richSnippets;
    }

    public function getOgData(): array
    {
        $elements = $this->getDocument()->findAll('css', 'meta[property^="og:"]');

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
        return $this->getLinkHref('link[rel="canonical"]');
    }

    public function hasLinkAlternateForLocale(string $localeCode): bool
    {
        return null !== $this->getDocument()->find('css', sprintf('link[rel="alternate"][hreflang="%s"]', strtolower($localeCode)));
    }

    public function getLinkRelAlternateForLocale(string $localeCode): string
    {
        return $this->getLinkHref(sprintf('link[rel="alternate"][hreflang="%s"]', strtolower($localeCode)));
    }

    public function hasNoIndexNoFollowTag(): bool
    {
        return null !== $this->getDocument()->find('css', 'meta[name="robots"][content="noindex, nofollow"]');
    }

    public function getCurrentUrl(): string
    {
        return $this->getSession()->getCurrentUrl();
    }

    private function getLinkHref(string $selector): string
    {
        $element = $this->getDocument()->find('css', $selector);
        if (null === $element) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), 'link', 'css', $selector);
        }

        return (string) $element->getAttribute('href');
    }
}
