<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Filter\Logic;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrSpecification implements FilterInterface
{
    private array $specifications;

    public function __construct(FilterInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    public function isSatisfiedBy(Request $request): bool
    {
        return (bool) array_reduce($this->specifications, function ($carry, FilterInterface $specification) use ($request) {
            if (null === $carry) {
                return $specification->isSatisfiedBy($request);
            }

            return $carry || $specification->isSatisfiedBy($request);
        });
    }
}
