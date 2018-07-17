<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

final class CollectionUpdateStruct
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
