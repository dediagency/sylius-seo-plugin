<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\Specification;

use Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter\NoIndexNoFollowFilterInterface;
use Symfony\Component\HttpFoundation\Request;

class IsTaxonFilter implements NoIndexNoFollowFilterInterface
{
    public function isSatisfiedBy(Request $request): bool
    {
        return 'sylius_shop_product_index' === $request->attributes->get('_route', '');
    }
}
