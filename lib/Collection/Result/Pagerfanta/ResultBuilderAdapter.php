<?php

namespace Netgen\BlockManager\Collection\Result\Pagerfanta;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Pagerfanta\Adapter\AdapterInterface;

final class ResultBuilderAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface
     */
    private $resultBuilder;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    private $collection;

    /**
     * @var int
     */
    private $startingOffset;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var int
     */
    private $totalCount;

    public function __construct(
        ResultBuilderInterface $resultBuilder,
        Collection $collection,
        $startingOffset = 0,
        $flags = 0
    ) {
        $this->resultBuilder = $resultBuilder;
        $this->collection = $collection;
        $this->startingOffset = $startingOffset;
        $this->flags = $flags;
    }

    public function getNbResults()
    {
        if ($this->totalCount === null) {
            $result = $this->resultBuilder->build($this->collection, 0, 0, $this->flags);
            $this->totalCount = $result->getTotalCount() - $this->startingOffset;
            $this->totalCount = $this->totalCount > 0 ? $this->totalCount : 0;
        }

        return $this->totalCount;
    }

    public function getSlice($offset, $length)
    {
        $result = $this->resultBuilder->build(
            $this->collection,
            $offset + $this->startingOffset,
            $length,
            $this->flags
        );

        if ($this->totalCount === null) {
            $this->totalCount = $result->getTotalCount() - $this->startingOffset;
            $this->totalCount = $this->totalCount > 0 ? $this->totalCount : 0;
        }

        return $result;
    }
}
