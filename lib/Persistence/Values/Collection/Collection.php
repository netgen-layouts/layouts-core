<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

class Collection extends Value
{
    /**
     * Collection ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Collection type. One of Collection::TYPE_* flags.
     *
     * @var int
     */
    public $type;

    /**
     * Human readable name of this collection.
     *
     * @var string
     */
    public $name;

    /**
     * Collection status. One of Collection::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
