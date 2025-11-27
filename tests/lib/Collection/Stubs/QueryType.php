<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Stubs;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;

final class QueryType implements QueryTypeInterface
{
    use ParameterDefinitionCollectionTrait;

    public string $name {
        get => $this->type;
    }

    public private(set) QueryTypeHandler $handler;

    /**
     * @param mixed[] $values
     */
    public function __construct(
        private(set) string $type,
        array $values = [],
        ?int $count = null,
        bool $isContextual = false,
        private(set) bool $isEnabled = true,
    ) {
        $this->handler = new QueryTypeHandler($values, $count, $isContextual);
        $this->parameterDefinitions = $this->handler->getParameterDefinitions();
    }

    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable
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
