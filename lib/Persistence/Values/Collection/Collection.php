<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

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
     * @const int
     */
    const TYPE_NAMED = 2;

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
     * @const int
     */
    const STATUS_TEMPORARY_DRAFT = 3;

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
