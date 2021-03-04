<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\Specification;

use Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\NoIndexNoFollowFilterInterface;
use Symfony\Component\HttpFoundation\Request;

class IsPaginatedFilter implements NoIndexNoFollowFilterInterface
{
    public function isSatisfiedBy(Request $request): bool
    {
        return '' !== $request->query->get('page', '');
    }
}
