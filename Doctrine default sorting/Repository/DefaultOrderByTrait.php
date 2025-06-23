<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Defines default sorting for entity repository.
 */
trait DefaultOrderByTrait
{
    /**
     * Returns array with default order by definition. Format is compatible
     * with findBy() method. Must be implemented by importing repository.
     */
    abstract private function getDefaultOrderBy(): array;

    /**
     * @see \Doctrine\ORM\EntityRepository::createQueryBuilder
     */
    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy);

        foreach ($this->getDefaultOrderBy() as $sort => $order) {
            $queryBuilder->addOrderBy($alias.'.'.$sort, $order);
        }

        return $queryBuilder;
    }

    /**
     * @see \Doctrine\ORM\EntityRepository::findBy
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $orderBy = $orderBy ?: $this->getDefaultOrderBy();

        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
}
