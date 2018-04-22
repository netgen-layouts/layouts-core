<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Query;

final class ContextualQueryRunner implements QueryRunnerInterface
{
    private static $queryCount = (PHP_INT_MAX - 1) / 2;

    public function runQuery(Query $query, $offset = 0, $limit = null)
    {
        for ($i = 0; $i < $limit; ++$i) {
            yield new Slot();
        }
    }

    public function count(Query $query)
    {
        return self::$queryCount;
    }
}
