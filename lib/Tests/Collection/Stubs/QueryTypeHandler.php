<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition\Integer;
use Netgen\BlockManager\Parameters\ParameterDefinition\TextLine;

class QueryTypeHandler implements QueryTypeHandlerInterface
{
    /**
     * @var array
     */
    protected $values = array();

    /**
     * @var int|null
     */
    protected $count;

    /**
     * Constructor.
     *
     * @param array $values
     * @param int $count
     */
    public function __construct(array $values = array(), $count = null)
    {
        $this->values = $values;
        $this->count = $count;
    }

    /**
     * Returns the array specifying query parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        return array(
            'offset' => new Integer(),
            'param' => new TextLine(),
        );
    }

    /**
     * Returns the values from the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     *
     * @return mixed[]
     */
    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return array_slice($this->values, $offset, $limit);
    }

    /**
     * Returns the value count from the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function getCount(Query $query)
    {
        if ($this->count !== null) {
            return $this->count;
        }

        return count($this->values);
    }

    /**
     * Returns the limit internal to this query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function getInternalLimit(Query $query)
    {
    }
}
