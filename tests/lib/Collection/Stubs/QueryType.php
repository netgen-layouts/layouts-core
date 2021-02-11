<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Stubs;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;

final class QueryType implements QueryTypeInterface
{
    use ParameterDefinitionCollectionTrait;

    private string $type;

    private bool $enabled;

    private QueryTypeHandler $handler;

    /**
     * @param mixed[] $values
     */
    public function __construct(string $type, array $values = [], ?int $count = null, bool $isContextual = false, bool $enabled = true)
    {
        $this->type = $type;
        $this->enabled = $enabled;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getName(): string
    {
        return $this->type;
    }
}
