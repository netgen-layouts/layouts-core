<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;

final class QueryTypeHandler implements QueryTypeHandlerInterface
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
    private $isContextual;

    public function __construct(array $values = array(), $count = null, $isContextual = false)
    {
        $this->values = $values;
        $this->count = $count;
        $this->isContextual = $isContextual;
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }

    public function getParameterDefinitions()
    {
        return array(
            'param' => new ParameterDefinition(
                array(
                    'name' => 'param',
                    'type' => new TextLineType(),
                    'isRequired' => true,
                    'options' => array(
                        'translatable' => false,
                    ),
                )
            ),
            'param2' => new ParameterDefinition(
                array(
                    'name' => 'param2',
                    'type' => new TextLineType(),
                    'options' => array(
                        'translatable' => true,
                    ),
                )
            ),
        );
    }

    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return array_slice($this->values, $offset, $limit);
    }

    public function getCount(Query $query)
    {
        if ($this->count !== null) {
            return $this->count;
        }

        return count($this->values);
    }

    public function isContextual(Query $query)
    {
        return $this->isContextual;
    }
}
