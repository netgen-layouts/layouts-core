<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\ValueObject;

class CollectionReferenceUpdateStruct extends ValueObject
{
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
