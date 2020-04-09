<?php

namespace App\Listing;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Listing helper wraps pagination and searching functionality for entity
 * repositories and query builders. It's intented to be used in controllers.
 * For pagination KnpPaginatorBundle is used under the hood.
 *
 * Example usage:
 * ```
 * return $this->render('user/list.html.twig', [
 *     'users' => $helper->list($userRepository),
 * ]);
 * ```
 * Example usage with custom query builder:
 * ```
 * return $this->render('user/list.html.twig', [
 *     'users' => $helper->list($userRepository, $userRepository->getQueryBuilderForAdminType()),
 * ]);
 * ```
 */
class ListingHelper
{
    private const PAGE_QUERY_STRING_PARAM = 'p';
    private const SEARCH_QUERY_STRING_PARAM = 's';

    public const DISABLE_SEARCH = 0b0001;
    public const DISABLE_PAGINATION = 0b0010;

    private $requestStack;
    private $paginator;
    private $paginationLimit;

    public function __construct(RequestStack $requestStack, PaginatorInterface $paginator,
                                int $paginationLimit = 10)
    {
        $this->requestStack = $requestStack;
        $this->paginator = $paginator;
        $this->paginationLimit = $paginationLimit;
    }

    /**
     * Enable pagination and search funtionality on given entity repository
     * and query builder. Bare query builder for given repository will be
     * created if none provided.
     *
     * Search feature is enabled by default. Exception is thrown if given
     * repository does not implement SearchableRepositoryInterface.
     * Use `DISABLE_SEARCH` constant in $options argument to disable it.
     *
     * Pagination is enabled by default. Use `DISABLE_PAGINATION` constant
     * in $options argument to disable this feature.
     *
     * Entity repository may implement ListingOptimizedRepositoryInterface to
     * apply query optimizations for listing purposes like LEFT/INNER JOINs.
     *
     * @see SearchableRepositoryInterface
     * @see ListingOptimizedRepositoryInterface
     */
    public function list(EntityRepository $repository, ?QueryBuilder $queryBuilder = null,
                         int $options = null): iterable
    {
        $queryBuilder = $queryBuilder ?? $repository->createQueryBuilder('x');
        $queryBuilderAlias = $queryBuilder->getRootAliases()[0];

        $request = $this->requestStack->getCurrentRequest();

        if (!($options & self::DISABLE_SEARCH)) {
            $searchTerm = trim($request->query->get(self::SEARCH_QUERY_STRING_PARAM, null));

            if (!($repository instanceof SearchableRepositoryInterface)) {
                throw new \InvalidArgumentException(
                    sprintf('Search is enabled for not searchable %s repository.', get_class($repository))
                );
            }

            if ($searchTerm) {
                $repository->addSearchTermToQueryBuilder($queryBuilder, $queryBuilderAlias, $searchTerm);
            }
        }

        if ($repository instanceof ListingOptimizedRepositoryInterface) {
            $repository->optimizeQueryBuilderForListing($queryBuilder, $queryBuilderAlias);
        }

        if ($options & self::DISABLE_PAGINATION) {
            return $queryBuilder->getQuery()->getResult();
        }

        return $this->paginator->paginate(
            $queryBuilder,
            $request->query->get(self::PAGE_QUERY_STRING_PARAM, 1),
            $this->paginationLimit,
            ['pageParameterName' => self::PAGE_QUERY_STRING_PARAM]
        );
    }
}
