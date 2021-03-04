<?php

declare(strict_types=1);

namespace Tests\Dedi\SyliusSEOPlugin\Behat\Page\Shop;

use InvalidArgumentException;

class PageCollection
{
    private array $pages;

    public function __construct(iterable $pages)
    {
        $this->pages = iterator_to_array($pages);
    }

    public function getPage(string $name): SeoPage
    {
        if (!array_key_exists($name, $this->pages)) {
            throw new InvalidArgumentException(sprintf('Unrecognized Page name %s', $name));
        }

        return $this->pages[$name];
    }

    public function getAll()
    {
        return $this->pages;
    }
}
