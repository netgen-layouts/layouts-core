<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

class Collection extends ValueObject
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
     * @const int
     */
    const STATUS_DRAFT = 0;

    /**
     * @const int
     */
    const STATUS_PUBLISHED = 1;

    /**
     * @const int
     */
    const STATUS_ARCHIVED = 2;

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
     * Indicates if the collection is shared.
     *
     * @var bool
     */
    public $shared;

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
