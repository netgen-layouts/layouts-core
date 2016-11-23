<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;

class ResultIteratorFactory
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    protected $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ItemLoaderInterface $itemLoader
     * @param \Netgen\BlockManager\Item\ItemBuilderInterface $itemBuilder
     */
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
     * @param \Netgen\BlockManager\Collection\Result\CollectionIterator $collectionIterator
     * @param int $flags
     *
     * @return \Iterator
     */
    public function getResultIterator(CollectionIterator $collectionIterator, $flags = 0)
    {
        return new ResultFilterIterator(
            new ResultBuilderIterator(
                $collectionIterator,
                $this->itemLoader,
                $this->itemBuilder
            ),
            $flags
        );
    }
}
