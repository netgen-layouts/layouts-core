<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\Value;

final class CollectionCreateStruct extends Value
{
    /**
     * The offset for the collection.
     *
     * @var int
     */
    public $offset = 0;

    /**
     * The limit for the collection.
     *
     * @var int|null
     */
    public $limit;

    /**
     * If set, the collection will have a query created from this query struct.
     *
     * @var \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct|null
     */
    public $queryCreateStruct;
}
