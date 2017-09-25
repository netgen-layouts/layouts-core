<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\ValueObject;

/**
 * @final
 */
class QueryType extends ValueObject implements QueryTypeInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    protected $config;

    public function getType()
    {
        return $this->type;
    }

    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return $this->handler->getValues($query, $offset, $limit);
    }

    public function getCount(Query $query)
    {
        $queryCount = $this->handler->getCount($query);

        $internalLimit = $this->handler->getInternalLimit($query);
        if ($internalLimit !== null && $queryCount > $internalLimit) {
            $queryCount = $internalLimit;
        }

        return $queryCount;
    }

    public function getInternalLimit(Query $query)
    {
        return $this->handler->getInternalLimit($query);
    }

    public function isContextual(Query $query)
    {
        return $this->handler->isContextual($query);
    }

    public function getConfig()
    {
        return $this->config;
    }
}
