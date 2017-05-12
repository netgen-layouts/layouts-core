<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Value;

class Collection extends Value
{
    /**
     * @const int
     */
    const TYPE_MANUAL = 0;

    /**
     * @const int
     */
    const TYPE_DYNAMIC = 1;

    /**
     * Collection ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Collection type. One of self::TYPE_* flags.
     *
     * @var int
     */
    public $type;

    /**
     * Collection status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
