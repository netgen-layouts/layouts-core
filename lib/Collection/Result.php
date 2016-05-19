<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\Value;

class Result extends Value
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    protected $collection;

    /**
     * @var \Netgen\BlockManager\Collection\ResultItem[]
     */
    protected $items;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $limit;

    /**
     * Returns the collection from which was this result generated.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Returns the items in this result.
     *
     * @return \Netgen\BlockManager\Collection\ResultItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Returns the offset with which was this result generated.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Returns the limit with which was this result generated.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
