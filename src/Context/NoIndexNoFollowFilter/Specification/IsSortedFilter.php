<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\Specification;

use Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\NoIndexNoFollowFilterInterface;
use Symfony\Component\HttpFoundation\Request;

class IsSortedFilter implements NoIndexNoFollowFilterInterface
{
    public function isSatisfiedBy(Request $request): bool
    {
        return !empty($request->query->get('sorting', false));
    }
}
