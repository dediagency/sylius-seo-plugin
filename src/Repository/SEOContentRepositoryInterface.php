<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Repository;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Doctrine\ORM\QueryBuilder;

interface SEOContentRepositoryInterface
{
    public function createListQueryBuilder(string $locale): QueryBuilder;

    public function findOneByUri(string $uri, string $locale): ?SEOContentInterface;
}
