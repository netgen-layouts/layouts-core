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
    private $type;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    private $handler;

    public function getType(): string
    {
        return $this->type;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return $this->handler->getValues($query, $offset, $limit);
    }

    public function getCount(Query $query): int
    {
        return $this->handler->getCount($query);
    }

    public function isContextual(Query $query): bool
    {
        return $this->handler->isContextual($query);
    }
}
