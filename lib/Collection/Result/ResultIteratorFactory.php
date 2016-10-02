<?php

namespace Netgen\BlockManager\Collection\Result;

use Traversable;

class ResultIteratorFactory
{
    /**
     * Builds and returns result iterator.
     *
     * @param \Traversable $traversable
     * @param int $flags
     *
     * @return \Iterator
     */
    public function getResultIterator(Traversable $traversable, $flags = 0)
    {
        return new ResultFilterIterator(
            new ResultBuilderIterator($traversable),
            $flags
        );
    }
}
