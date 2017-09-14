<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Value;

class Item extends Value
{
    /**
     * @const int
     */
    const TYPE_MANUAL = 0;

    /**
     * @const int
     */
    const TYPE_OVERRIDE = 1;

    /**
     * Item ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * ID of the collection to which this item belongs.
     *
     * @var int|string
     */
    public $collectionId;

    /**
     * Position of item within the collection.
     *
     * @var int
     */
    public $position;

    /**
     * Type of the item. One of self::TYPE_* flags.
     *
     * @var int
     */
    public $type;

    /**
     * ID of value from CMS this item wraps.
     *
     * @var int|string
     */
    public $valueId;

    /**
     * Type of value from CMS this item wraps.
     *
     * @var string
     */
    public $valueType;

    /**
     * Item status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
