<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

class Item extends Value
{
    /**
     * Item ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Collection ID to which this item belongs.
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
     * Type of the item. One of Item::TYPE_* flags.
     *
     * @var int
     */
    public $type;

    /**
     * ID of value this item holds.
     *
     * @var int|string
     */
    public $valueId;

    /**
     * Type of value this item holds.
     *
     * @var string
     */
    public $valueType;

    /**
     * Item status. One of Collection::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
