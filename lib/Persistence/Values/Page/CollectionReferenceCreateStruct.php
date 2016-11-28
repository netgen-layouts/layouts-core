<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

use Netgen\BlockManager\ValueObject;

class CollectionReferenceCreateStruct extends ValueObject
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public $collection;

    /**
     * @var int
     */
    public $offset;

    /**
     * @var int
     */
    public $limit;
}
