<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\Parameter\Integer;
use Netgen\BlockManager\Parameters\Parameter\TextLine;
use ArrayIterator;

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
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
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
     * @param array $parameters
     * @param int $offset
     * @param int $limit
     *
     * @return \Iterator
     */
    public function getValues(array $parameters, $offset = 0, $limit = null)
    {
        return new ArrayIterator(array_slice($this->values, $offset, $limit));
    }

    /**
     * Returns the value count from the query.
     *
     * @param array $parameters
     *
     * @return int
     */
    public function getCount(array $parameters)
    {
        if ($this->count !== null) {
            return $this->count;
        }

        return count($this->values);
    }

    /**
     * Returns the name of the parameter which will be used as a limit inside the query.
     *
     * @return string
     */
    public function getLimitParameter()
    {
        return null;
    }
}
