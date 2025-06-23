<?php

namespace App\Listing;

use Doctrine\ORM\QueryBuilder;

/**
 * Implement this interface in entity repository class to enable search feature
 * in listing helper.
 *
 * @see ListingHelper
 */
interface SearchableRepositoryInterface
{
    /**
     * Add search term to given query to enable searching in listing helper.
     *
     * Example implementation:
     * ```
     * $queryBuilder
     *     ->andWhere("$alias.username LIKE :searchTerm")
     *     ->setParameter('searchTerm', '%' . addcslashes($searchTerm, '%_') . '%')
     * ;
     * ```
     *
     * @see ListingHelper
     */
    public function addSearchTermToQueryBuilder(QueryBuilder $queryBuilder, string $alias, string $searchTerm): void;
}
