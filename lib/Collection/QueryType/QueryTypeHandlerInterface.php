<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\QueryType;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;

/**
 * Query type handler represents the dynamic/runtime part of the query type.
 * It is mainly used to implement the fetching of items from the CMS.
 *
 * Implement this interface to create your own query types.
 */
interface QueryTypeHandlerInterface
{
    /**
     * Use this group in your parameters if you wish to show them in the
     * Advanced section of query edit interface.
     */
    public const GROUP_ADVANCED = 'advanced';

    /**
     * Builds the parameters by using provided parameter builder.
     */
    public function buildParameters(ParameterBuilderInterface $builder): void;

    /**
     * Returns the values from the query.
     *
     * @return iterable<object>
     */
    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable;

    /**
     * Returns the value count from the query.
     */
    public function getCount(Query $query): int;

    /**
     * Returns if the provided query is dependent on a context, i.e. currently displayed page.
     */
    public function isContextual(Query $query): bool;
}
