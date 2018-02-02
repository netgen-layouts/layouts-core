<?php

namespace Netgen\BlockManager\Collection\Result;

use Iterator;
use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
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

    /**
     * @var \Netgen\BlockManager\Collection\Item\VisibilityResolverInterface
     */
    private $visibilityResolver;

    public function __construct(
        ItemLoaderInterface $itemLoader,
        ItemBuilderInterface $itemBuilder,
        VisibilityResolverInterface $visibilityResolver
    ) {
        $this->itemLoader = $itemLoader;
        $this->itemBuilder = $itemBuilder;
        $this->visibilityResolver = $visibilityResolver;
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
                $this->itemBuilder,
                $this->visibilityResolver
            ),
            $flags
        );
    }
}
