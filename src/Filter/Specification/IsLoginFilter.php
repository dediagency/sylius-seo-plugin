<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Filter\Specification;

use Dedi\SyliusSEOPlugin\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class IsLoginFilter implements FilterInterface
{
    public function isSatisfiedBy(Request $request): bool
    {
        $currentRoute = $request->attributes->get('_route', '');
        $routeFilters = ['sylius_shop_login', 'sylius_shop_request_password_reset_token', 'sylius_shop_register'];
        $isLoginFilter = array_filter($routeFilters, static function ($route) use ($currentRoute) {
            return $route === $currentRoute;
        });

        return count($isLoginFilter) > 0;
    }
}
