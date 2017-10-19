<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\ValueObject;

final class CollectionUpdateStruct extends ValueObject
{
    /**
     * The new offset for the collection.
     *
     * @var int
     */
    public $offset;

    /**
     * The new limit for the collection.
     *
     * Set to 0 to disable the limit.
     *
     * @var int
     */
    public $limit;
}
