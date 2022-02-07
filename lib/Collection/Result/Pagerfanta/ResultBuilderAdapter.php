<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result\Pagerfanta;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Collection\Result\ResultBuilderInterface;
use Netgen\Layouts\Collection\Result\ResultSet;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Netgen\Layouts\Collection\Result\Result>
 */
final class ResultBuilderAdapter implements AdapterInterface
{
    private ResultBuilderInterface $resultBuilder;

    private Collection $collection;

    private int $startingOffset;

    /**
     * @var int<0, max>|null
     */
    private ?int $maxTotalCount;

    private int $flags;

    /**
     * @var int<0, max>
     */
    private int $totalCount;

    /**
     * @param int<0, max>|null $maxTotalCount
     */
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
        if (!isset($this->totalCount)) {
            $result = $this->resultBuilder->build($this->collection, 0, 0, $this->flags);
            $this->setTotalCount($result);
        }

        return $this->totalCount;
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @return iterable<int, \Netgen\Layouts\Collection\Result\Result>
     */
    public function getSlice($offset, $length): iterable
    {
        $result = $this->resultBuilder->build(
            $this->collection,
            $offset + $this->startingOffset,
            $length,
            $this->flags,
        );

        if (!isset($this->totalCount)) {
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
        /** @var int<0, max> $totalCount */
        $totalCount = $result->getTotalCount() - $this->startingOffset;
        $this->totalCount = $totalCount > 0 ? $totalCount : 0;

        if ($this->maxTotalCount !== null && $this->totalCount >= $this->maxTotalCount) {
            $this->totalCount = $this->maxTotalCount;
        }
    }
}
