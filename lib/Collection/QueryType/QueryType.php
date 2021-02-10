<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\QueryType;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;

/**
 * @final
 */
class QueryType implements QueryTypeInterface
{
    use HydratorTrait;
    use ParameterDefinitionCollectionTrait;

    private string $type;

    private bool $isEnabled;

    private string $name;

    private QueryTypeHandlerInterface $handler;

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
