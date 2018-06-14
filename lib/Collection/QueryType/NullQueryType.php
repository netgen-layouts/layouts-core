<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\QueryType;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;

final class NullQueryType implements QueryTypeInterface
{
    use ParameterDefinitionCollectionTrait;

    /**
     * @var string
     */
    private $queryType;

    /**
     * @param string $queryType
     */
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

    public function getValues(Query $query, $offset = 0, $limit = null)
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
