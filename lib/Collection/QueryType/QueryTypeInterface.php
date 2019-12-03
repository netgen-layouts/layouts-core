<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\QueryType;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;

/**
 * Query type represents a model of the query which is used to inject
 * items from CMS to a block collection.
 */
interface QueryTypeInterface extends ParameterDefinitionCollectionInterface
{
    /**
     * Returns the query type.
     */
    public function getType(): string;

    /**
     * Returns if the query type is enabled or not.
     */
    public function isEnabled(): bool;

    /**
     * Returns the query type name.
     */
    public function getName(): string;

    /**
     * Returns the values from the query.
     *
     * @return iterable<object>
     */
    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable;

    /**
     * Returns the value count from the query.
     *
     * To the outside world, query count is whatever the query returns
     * based on parameter values. This may not correspond to inner query count
     * when parameters themselves contain offset and limit parameters which are then
     * used for inner query.
     *
     * Due to that, this method takes the inner query limit (as used in parameters)
     * and returns it instead if returned count is larger.
     */
    public function getCount(Query $query): int;

    /**
     * Returns if the provided query is dependent on a context, i.e. currently displayed page.
     */
    public function isContextual(Query $query): bool;
}
