<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Filter\Specification;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class IsPaginatedFilter implements FilterInterface
{
    public function isSatisfiedBy(Request $request): bool
    {
        $page = $request->query->get('page');

        if (null === $page) {
            return true;
        }

        return $page > 1;
    }
}
