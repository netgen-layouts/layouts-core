<?php

namespace Netgen\BlockManager\Collection;

class Result
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public $collection;

    /**
     * @var \Netgen\BlockManager\Collection\ResultValue[]
     */
    public $values;

    /**
     * @var int
     */
    public $offset;

    /**
     * @var int
     */
    public $limit;
}
