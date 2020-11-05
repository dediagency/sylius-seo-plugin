<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop;

interface RichSnippetAwarePageInterface
{
    /**
     * @return array where the key is the name of the rich snippet
     */
    public function getRichSnippetData(): array;

    public function getOgData(): array;
}
