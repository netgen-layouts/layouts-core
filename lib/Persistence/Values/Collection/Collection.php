<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Value;

class Collection extends Value
{
    /**
     * Collection ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Collection status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
