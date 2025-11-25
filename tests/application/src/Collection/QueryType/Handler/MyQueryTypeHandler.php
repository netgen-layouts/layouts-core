<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Collection\QueryType\Handler;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType\TextLineType;
use Netgen\Layouts\Tests\App\Item\Value;

final class MyQueryTypeHandler implements QueryTypeHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add('param', TextLineType::class, ['required' => true, 'translatable' => false]);
        $builder->add('param2', TextLineType::class, ['required' => false, 'translatable' => true]);
    }

    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable
    {
        return [new Value(140), new Value(79), new Value(78)];
    }

    public function getCount(Query $query): int
    {
        return 3;
    }

    public function isContextual(Query $query): bool
    {
        return false;
    }
}
