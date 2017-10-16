<?php

namespace Netgen\BlockManager\Collection\Result;

use Iterator;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;

final class ResultIteratorFactory
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
     * @param int $flags
     *
     * @return \Iterator
     */
    public function getResultIterator(Iterator $iterator, $flags = 0)
    {
        return new ResultFilterIterator(
            new ResultBuilderIterator(
                $iterator,
                $this->itemLoader,
                $this->itemBuilder
            ),
            $flags
        );
    }
}
