<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface SeoPage extends PageInterface
{
    /**
     * @return array where the key is the name of the rich snippet
     */
    public function getRichSnippetData(): array;

    public function getOgData(): array;

    public function hasLinkRelCanonical(): bool;

    public function getLinkRelCanonical(): string;

    public function hasLinkAlternateForLocale(string $localeCode): bool;

    public function getLinkRelAlternateForLocale(string $localeCode): string;

    public function hasNoIndexNoFollowTag(): bool;

    public function getCurrentUrl(): string;
}
