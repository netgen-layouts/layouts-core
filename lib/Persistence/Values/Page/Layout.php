<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

use Netgen\BlockManager\ValueObject;

class Layout extends ValueObject
{
    /**
     * @const string
     */
    const STATUS_DRAFT = 0;

    /**
     * @const string
     */
    const STATUS_PUBLISHED = 1;

    /**
     * @const string
     */
    const STATUS_ARCHIVED = 2;

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
     * Layout status. One of Layout::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
