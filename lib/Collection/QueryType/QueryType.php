<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\QueryType;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;
use Netgen\BlockManager\Value;

/**
 * @final
 */
class QueryType extends Value implements QueryTypeInterface
{
    use ParameterDefinitionCollectionTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $isEnabled;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    protected $handler;

    public function getType()
    {
        return $this->type;
    }

    public function isEnabled()
    {
        return $this->isEnabled;
    }

    public function getName()
    {
        return $this->name;
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
}
