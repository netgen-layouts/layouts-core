<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

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
     * Layout parent ID.
     *
     * @var int|string
     */
    public $parentId;

    /**
     * Layout identifier.
     *
     * @var string
     */
    public $identifier;

    /**
     * Human readable layout name.
     *
     * @var string
     */
    public $name;

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
}
