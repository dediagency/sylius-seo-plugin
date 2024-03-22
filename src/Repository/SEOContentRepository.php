<?php

declare(strict_types=1);

namespace Dedi\SyliusSEOPlugin\Repository;

use Dedi\SyliusSEOPlugin\Entity\SEOContentInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class SEOContentRepository extends EntityRepository implements SEOContentRepositoryInterface
{
    public function createListQueryBuilder(string $locale): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->addSelect('robot')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->leftJoin('o.robots', 'robot', 'WITH', 'robot.locale = :locale')
            ->setParameter('locale', $locale)
        ;
    }

    public function findOneByUri(string $uri, string $locale): ?SEOContentInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->createQueryBuilder('o')
            ->leftJoin('o.translations', 'translation')
            ->andWhere('translation.uri = :uri')
            ->andWhere('translation.locale = :locale')
            ->setParameter('uri', $uri)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
