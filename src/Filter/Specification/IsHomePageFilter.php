<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Filter\Specification;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class IsHomePageFilter implements FilterInterface
{
    public function isSatisfiedBy(Request $request): bool
    {
        return 'sylius_shop_homepage' === $request->attributes->get('_route', '');
    }
}
