<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\Logic;

use Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\NoIndexNoFollowFilterInterface;
use Symfony\Component\HttpFoundation\Request;

final class AndSpecification implements NoIndexNoFollowFilterInterface
{
    /** @var NoIndexNoFollowFilterInterface[] */
    private array $specifications;

    public function __construct(NoIndexNoFollowFilterInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    public function isSatisfiedBy(Request $request): bool
    {
        if ([] === $this->specifications) {
            return false;
        }

        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($request)) {
                return false;
            }
        }

        return true;
    }
}
