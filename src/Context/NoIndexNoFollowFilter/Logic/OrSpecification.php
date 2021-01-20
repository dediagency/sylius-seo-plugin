<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\Logic;

use Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\NoIndexNoFollowFilterInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrSpecification implements NoIndexNoFollowFilterInterface
{
    private array $specifications;

    public function __construct(NoIndexNoFollowFilterInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    public function isSatisfiedBy(Request $request): bool
    {
        return array_reduce($this->specifications, function ($carry, NoIndexNoFollowFilterInterface $specification) use ($request) {
            if (null === $carry) {
                return $specification->isSatisfiedBy($request);
            }

            return $carry || $specification->isSatisfiedBy($request);
        });
    }
}
