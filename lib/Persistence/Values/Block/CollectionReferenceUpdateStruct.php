<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\ValueObject;

class CollectionReferenceUpdateStruct extends ValueObject
{
    /**
     * The collection to link to.
     *
     * @var \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public $collection;

    /**
     * Starting offset for the collection results.
     *
     * @var int
     */
    public $offset;

    /**
     * Starting limit for the collection results.
     *
     * @var int
     */
    public $limit;
}
