<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result\Pagerfanta;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

final class PagerFactory
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface
     */
    private $resultBuilder;

    /**
     * @var int
     */
    private $maxLimit;

    public function __construct(ResultBuilderInterface $resultBuilder, int $maxLimit)
    {
        $this->resultBuilder = $resultBuilder;
        $this->maxLimit = $maxLimit;
    }

    /**
     * Builds and returns the Pagerfanta pager for provided collection.
     *
     * The pager starting page will be set to $startPage.
     */
    public function getPager(Collection $collection, int $startPage, int $maxPages = null, int $flags = 0): Pagerfanta
    {
        $maxTotalCount = null;
        if (is_int($maxPages) && $maxPages > 0) {
            $maxTotalCount = $maxPages * $this->getMaxPerPage($collection);
        }

        $pagerAdapter = new ResultBuilderAdapter(
            $this->resultBuilder,
            $collection,
            $collection->getOffset(),
            $maxTotalCount,
            $flags
        );

        return $this->buildPager($pagerAdapter, $collection, (int) $startPage);
    }

    /**
     * Builds the pager from provided adapter.
     */
    private function buildPager(AdapterInterface $adapter, Collection $collection, int $startPage): Pagerfanta
    {
        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($this->getMaxPerPage($collection));
        $pager->setCurrentPage($startPage);

        return $pager;
    }

    /**
     * Returns the maximum number of items per page for the provided collection,
     * while taking into account the injected maximum number of items.
     */
    private function getMaxPerPage(Collection $collection): int
    {
        $limit = $collection->getLimit();

        return $limit > 0 && $limit < $this->maxLimit ? $limit : $this->maxLimit;
    }
}
