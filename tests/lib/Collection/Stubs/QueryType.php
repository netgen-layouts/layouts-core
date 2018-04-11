<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Exception\Parameters\ParameterException;

final class QueryType implements QueryTypeInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var \Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler
     */
    private $handler;

    public function __construct($type, array $values = array(), $count = null, $isContextual = false)
    {
        $this->type = $type;

        $this->handler = new QueryTypeHandler($values, $count, $isContextual);
    }

    public function getParameterDefinitions()
    {
        return $this->handler->getParameterDefinitions();
    }

    public function getParameterDefinition($parameterName)
    {
        if ($this->hasParameterDefinition($parameterName)) {
            return $this->handler->getParameterDefinitions()[$parameterName];
        }

        throw new ParameterException('parameterName', 'Parameter is missing.');
    }

    public function hasParameterDefinition($parameterName)
    {
        return isset($this->handler->getParameterDefinitions()[$parameterName]);
    }

    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return $this->handler->getValues($query, $offset, $limit);
    }

    public function getCount(Query $query)
    {
        return $this->handler->getCount($query);
    }

    public function isContextual(Query $query)
    {
        return $this->handler->isContextual($query);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->type;
    }

    public function getForms()
    {
        return array();
    }

    public function hasForm($formName)
    {
        return false;
    }

    public function getForm($formName)
    {
    }
}
