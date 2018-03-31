<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Query;

final class ContextualQueryRunner implements QueryRunnerInterface
{
    public function __invoke(Query $query, $offset = 0, $limit = null)
    {
        for ($i = 0; $i < $limit; ++$i) {
            yield new Slot();
        }
    }

    public function count(Query $query)
    {
        return 0;
    }
}
