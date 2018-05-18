<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;

final class QueryType implements QueryTypeInterface
{
    use ParameterDefinitionCollectionTrait;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var \Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler
     */
    private $handler;

    public function __construct($type, array $values = [], $count = null, $isContextual = false, $enabled = true)
    {
        $this->type = $type;
        $this->enabled = $enabled;

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

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getName()
    {
        return $this->type;
    }
}
