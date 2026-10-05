<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Filter;

interface FilterRegistryInterface
{
    public function getFilter(string $name): FilterInterface;

    public function getAll(): array;
}
