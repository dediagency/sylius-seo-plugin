<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter;

use InvalidArgumentException;

class FilterRegistry
{
    /** @var array<string, NoIndexNoFollowFilterInterface> */
    private array $filters;

    /** @param iterable<string, NoIndexNoFollowFilterInterface> $filters */
    public function __construct(iterable $filters)
    {
        $this->filters = iterator_to_array($filters);
    }

    public function getFilter(string $name): NoIndexNoFollowFilterInterface
    {
        if (!array_key_exists($name, $this->filters)) {
            throw new InvalidArgumentException(sprintf('Unrecognized Filter identifier %s', $name));
        }

        return $this->filters[$name];
    }

    /** @return array<string, NoIndexNoFollowFilterInterface> */
    public function getAll(): array
    {
        return $this->filters;
    }
}
