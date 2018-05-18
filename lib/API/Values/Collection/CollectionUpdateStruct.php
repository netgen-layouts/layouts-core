<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\Value;

final class CollectionUpdateStruct extends Value
{
    /**
     * The new offset for the collection.
     *
     * @var int|null
     */
    public $offset;

    /**
     * The new limit for the collection.
     *
     * Set to 0 to disable the limit.
     *
     * @var int|null
     */
    public $limit;
}
