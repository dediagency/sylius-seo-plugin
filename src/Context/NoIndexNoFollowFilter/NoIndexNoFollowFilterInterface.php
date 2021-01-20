<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Context\NoIndexNoFollowFilter;

use Symfony\Component\HttpFoundation\Request;

interface NoIndexNoFollowFilterInterface
{
    public function isSatisfiedBy(Request $request): bool;
}
