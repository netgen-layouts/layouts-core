<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Persistence\Values\Value;

class Layout extends Value
{
    /**
     * Layout ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Layout type.
     *
     * @var string
     */
    public $type;

    /**
     * Human readable layout name.
     *
     * @var string
     */
    public $name;

    /**
     * Flag indicating if this layout is shared.
     *
     * @var bool
     */
    public $shared;

    /**
     * Timestamp when the layout was created.
     *
     * @var int
     */
    public $created;

    /**
     * Timestamp when the layout was last updated.
     *
     * @var int
     */
    public $modified;

    /**
     * Layout status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
