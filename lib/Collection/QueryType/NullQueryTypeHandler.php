<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\QueryType;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;

final class NullQueryTypeHandler implements QueryTypeHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder): void {}

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
