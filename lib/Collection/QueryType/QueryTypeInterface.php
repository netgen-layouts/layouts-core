<?php

namespace Netgen\BlockManager\Collection\QueryType;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;

/**
 * Query type represents a model of the query which is used to inject
 * items from CMS to a block collection.
 */
interface QueryTypeInterface extends ParameterDefinitionCollectionInterface
{
    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns if the query type is enabled or not.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Returns the query type name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the values from the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     *
     * @return mixed[]
     */
    public function getValues(Query $query, $offset = 0, $limit = null);

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
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function getCount(Query $query);

    /**
     * Returns if the provided query is dependent on a context, i.e. currently displayed page.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return bool
     */
    public function isContextual(Query $query);
}
