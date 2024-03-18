<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Filter\Specification;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class IsPaginatedFilter implements FilterInterface
{
    public function isSatisfiedBy(Request $request): bool
    {
        return false !== (bool) $request->query->get('page', false);
    }
}
