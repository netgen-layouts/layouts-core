<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Iterator;
use Netgen\BlockManager\API\Values\Collection\Query;

final class ContextualQueryRunner implements QueryRunnerInterface
{
    public function runQuery(Query $query, int $offset = 0, int $limit = null): Iterator
    {
        for ($i = 0; $i < $limit; ++$i) {
            yield new Slot();
        }
    }

    public function count(Query $query): int
    {
        return intdiv(PHP_INT_MAX - 1, 2);
    }
}
