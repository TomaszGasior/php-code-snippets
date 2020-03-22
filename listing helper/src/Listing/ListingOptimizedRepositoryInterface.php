<?php

namespace App\Listing;

use Doctrine\ORM\QueryBuilder;

/**
 * Implement this interface in entity repository class to optimize queries
 * executed by listing helper.
 *
 * @see ListingHelper
 */
interface ListingOptimizedRepositoryInterface
{
    /**
     * Optimize given query for listing helper.
     *
     * Example implementation:
     * ```
     * $queryBuilder
     *     ->leftJoin("$alias.files", 'files')
     *     ->addSelect('files')
     * ;
     * ```
     *
     * @see ListingHelper
     */
    public function optimizeQueryBuilderForListing(QueryBuilder $queryBuilder, string $alias): void;
}
