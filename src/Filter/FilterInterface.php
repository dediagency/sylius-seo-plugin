<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Filter;

use Symfony\Component\HttpFoundation\Request;

interface FilterInterface
{
    public function isSatisfiedBy(Request $request): bool;
}
