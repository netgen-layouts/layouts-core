<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

/**
 * Query type represents a model of the query which is used to inject
 * items from CMS to a block collection.
 */
interface QueryTypeInterface extends ParameterCollectionInterface
{
    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the query type name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns all forms.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Form[]
     */
    public function getForms();

    /**
     * Returns if the query type has a form with provided name.
     *
     * @param $formName
     *
     * @return bool
     */
    public function hasForm($formName);

    /**
     * Returns the form for provided form name.
     *
     * @param $formName
     *
     * @throws \Netgen\BlockManager\Exception\Collection\QueryTypeException If query type does not have the form
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Form
     */
    public function getForm($formName);

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
