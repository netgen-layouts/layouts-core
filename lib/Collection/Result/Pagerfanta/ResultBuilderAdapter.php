<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result\Pagerfanta;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Collection\Result\ResultBuilderInterface;
use Netgen\Layouts\Collection\Result\ResultSet;
use Pagerfanta\Adapter\AdapterInterface;

final class ResultBuilderAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Layouts\Collection\Result\ResultBuilderInterface
     */
    private $resultBuilder;

    /**
     * @var \Netgen\Layouts\API\Values\Collection\Collection
     */
    private $collection;

    /**
     * @var int
     */
    private $startingOffset;

    /**
     * @var int|null
     */
    private $maxTotalCount;

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
        int $startingOffset = 0,
        ?int $maxTotalCount = null,
        int $flags = 0
    ) {
        $this->resultBuilder = $resultBuilder;
        $this->collection = $collection;
        $this->startingOffset = $startingOffset;
        $this->maxTotalCount = $maxTotalCount;
        $this->flags = $flags;
    }

    public function getNbResults(): int
    {
        if ($this->totalCount === null) {
            $result = $this->resultBuilder->build($this->collection, 0, 0, $this->flags);
            $this->setTotalCount($result);
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
            $this->setTotalCount($result);
        }

        return $result;
    }

    /**
     * Sets the total count of the results to the adapter, while taking into account
     * the injected maximum number of pages to use.
     */
    private function setTotalCount(ResultSet $result): void
    {
        $this->totalCount = $result->getTotalCount() - $this->startingOffset;
        $this->totalCount = $this->totalCount > 0 ? $this->totalCount : 0;

        if ($this->maxTotalCount !== null && $this->totalCount >= $this->maxTotalCount) {
            $this->totalCount = $this->maxTotalCount;
        }
    }
}
