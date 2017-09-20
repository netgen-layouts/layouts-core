<?php

namespace Netgen\BlockManager\Collection\Result;

use Iterator;
use LimitIterator;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;

class ResultIteratorFactory
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    private $itemBuilder;

    public function __construct(
        ItemLoaderInterface $itemLoader,
        ItemBuilderInterface $itemBuilder
    ) {
        $this->itemLoader = $itemLoader;
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Builds and returns result iterator from provided collection iterator.
     *
     * @param \Iterator $iterator
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Iterator
     */
    public function getResultIterator(Iterator $iterator, $offset = 0, $limit = null, $flags = 0)
    {
        return new LimitIterator(
            new ResultFilterIterator(
                new ResultBuilderIterator(
                    $iterator,
                    $this->itemLoader,
                    $this->itemBuilder
                ),
                $flags
            ),
            $offset >= 0 ? $offset : 0,
            $limit > 0 ? $limit : -1
        );
    }
}
