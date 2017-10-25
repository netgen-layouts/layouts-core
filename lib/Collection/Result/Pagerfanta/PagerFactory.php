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
     * @param int $flags
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function getPager(Collection $collection, $startPage, $flags = 0)
    {
        $pagerAdapter = new ResultBuilderAdapter(
            $this->resultBuilder,
            $collection,
            $collection->getOffset(),
            $flags
        );

        $pager = $this->buildPager($pagerAdapter, $collection->getLimit());
        $pager->setCurrentPage((int) $startPage);

        return $pager;
    }

    /**
     * Builds the pager from provided adapter.
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param int $limit
     *
     * @return \Pagerfanta\Pagerfanta
     */
    private function buildPager(AdapterInterface $adapter, $limit)
    {
        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 && $limit < $this->maxLimit ? $limit : $this->maxLimit);

        return $pager;
    }
}
