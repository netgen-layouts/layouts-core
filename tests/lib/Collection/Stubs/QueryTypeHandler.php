<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType\IntegerType;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

class QueryTypeHandler implements QueryTypeHandlerInterface
{
    /**
     * @var array
     */
    private $values = array();

    /**
     * @var int|null
     */
    private $count;

    /**
     * @var bool
     */
    private $contextual;

    /**
     * Constructor.
     *
     * @param array $values
     * @param int $count
     * @param int $internalLimit
     * @param bool $contextual
     */
    public function __construct(array $values = array(), $count = null, $contextual = false)
    {
        $this->values = $values;
        $this->count = $count;
        $this->contextual = $contextual;
    }

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
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
            'offset' => new Parameter(
                array(
                    'name' => 'offset',
                    'type' => new IntegerType(),
                    'options' => array(
                        'translatable' => true,
                    ),
                ),
                true
            ),
            'param' => new Parameter(
                array(
                    'name' => 'param',
                    'type' => new TextLineType(),
                    'options' => array(
                        'translatable' => false,
                    ),
                ),
                true
            ),
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
     * Returns if the provided query is dependent on a context, i.e. current request.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return bool
     */
    public function isContextual(Query $query)
    {
        return $this->contextual;
    }
}
