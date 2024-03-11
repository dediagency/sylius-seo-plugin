<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter;

use InvalidArgumentException;
use Traversable;

class FilterRegistry
{
    private array $filters;

    public function __construct(Traversable $filters)
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

    public function getAll(): array
    {
        return $this->filters;
    }
}
