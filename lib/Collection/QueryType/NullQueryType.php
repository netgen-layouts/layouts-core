<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\QueryType;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;

final class NullQueryType implements QueryTypeInterface
{
    use ParameterDefinitionCollectionTrait;

    private string $queryType;

    public function __construct(string $queryType)
    {
        $this->queryType = $queryType;
    }

    public function getType(): string
    {
        return $this->queryType;
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'Invalid query type';
    }

    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable
    {
        return [];
    }

    public function getCount(Query $query): int
    {
        return 0;
    }

    public function isContextual(Query $query): bool
    {
        return false;
    }
}
