<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerFixturesBundle\Collection\QueryType\Handler;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;

final class MyQueryType implements QueryTypeHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add('param', TextLineType::class, ['required' => true, 'translatable' => false]);
        $builder->add('param2', TextLineType::class, ['required' => false, 'translatable' => true]);
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
