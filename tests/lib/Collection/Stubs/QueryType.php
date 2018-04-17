<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;

final class QueryType implements QueryTypeInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler
     */
    private $handler;

    public function __construct($type, array $values = [], $count = null, $isContextual = false)
    {
        $this->type = $type;

        $this->handler = new QueryTypeHandler($values, $count, $isContextual);
        $this->parameterDefinitions = $this->handler->getParameterDefinitions();
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
        return [];
    }

    public function hasForm($formName)
    {
        return false;
    }

    public function getForm($formName)
    {
    }
}
