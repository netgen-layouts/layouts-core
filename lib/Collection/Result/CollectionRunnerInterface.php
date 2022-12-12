<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Iterator;
use Netgen\Layouts\API\Values\Collection\Collection;

interface CollectionRunnerInterface
{
    /**
     * Runs the provided collection with offset and limit and returns
     * the iterator which can be used to iterate over the results.
     *
     * @return \Iterator<int, \Netgen\Layouts\Collection\Result\Result>
     */
    public function runCollection(Collection $collection, int $offset, int $limit, int $flags = 0): Iterator;

    /**
     * Returns the count of items in the provided collection.
     */
    public function count(Collection $collection): int;
}
