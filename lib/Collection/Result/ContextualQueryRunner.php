<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Iterator;
use Netgen\Layouts\API\Values\Collection\Query;

use function intdiv;

use const PHP_INT_MAX;

final class ContextualQueryRunner implements QueryRunnerInterface
{
    public function runQuery(Query $query, int $offset = 0, ?int $limit = null): Iterator
    {
        for ($i = 0; $i < $limit; ++$i) {
            yield new UnknownItem();
        }
    }

    public function count(Query $query): int
    {
        return intdiv(PHP_INT_MAX - 1, 2);
    }
}
