<?php

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

    public function __construct(ResultBuilderInterface $resultBuilder, $maxLimit)
    {
        $this->resultBuilder = $resultBuilder;
        $this->maxLimit = $maxLimit;
    }

    /**
     * Builds and returns the Pagerfanta pager for provided collection.
     *
     * The pager starting page will be set to $startPage.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $startPage
     * @param int $maxPages
     * @param int $flags
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function getPager(Collection $collection, $startPage, $maxPages = null, $flags = 0)
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
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $startPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    private function buildPager(AdapterInterface $adapter, Collection $collection, $startPage)
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
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return int
     */
    private function getMaxPerPage(Collection $collection)
    {
        $limit = $collection->getLimit();

        return $limit > 0 && $limit < $this->maxLimit ? $limit : $this->maxLimit;
    }
}
