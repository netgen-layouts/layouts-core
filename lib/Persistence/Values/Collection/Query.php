<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

class Query extends Value
{
    /**
     * Query ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Collection ID to which this query belongs.
     *
     * @var int|string
     */
    public $collectionId;

    /**
     * Position of query within the collection.
     *
     * @var int
     */
    public $position;

    /**
     * Query identifier.
     *
     * @var string
     */
    public $identifier;

    /**
     * Query type.
     *
     * @var string
     */
    public $type;

    /**
     * Query parameters.
     *
     * @var array
     */
    public $parameters;

    /**
     * Item status. One of Collection::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
