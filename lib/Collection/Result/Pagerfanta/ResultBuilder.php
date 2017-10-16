<?php

namespace Netgen\BlockManager\Collection\Result\Pagerfanta;

use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

final class ResultBuilder
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

    public function build(CollectionReference $collectionReference, $flags = 0)
    {
        $pagerAdapter = new ResultBuilderAdapter(
            $this->resultBuilder,
            $collectionReference->getCollection(),
            $collectionReference->getOffset(),
            $flags
        );

        return $this->buildPager($pagerAdapter, $collectionReference->getLimit());
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
        $pager->setMaxPerPage($limit > 0 ? $limit : $this->maxLimit);

        return $pager;
    }
}
