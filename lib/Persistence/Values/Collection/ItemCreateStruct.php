<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

class ItemCreateStruct extends ValueObject
{
    /**
     * Position of the new item in the collection.
     *
     * @var int
     */
    public $position;

    /**
     * ID of value from CMS for the new item.
     *
     * @var int|string
     */
    public $valueId;

    /**
     * Type of value from CMS for the new item.
     *
     * @var string
     */
    public $valueType;

    /**
     * Type of the item. One of Item::TYPE_* flags.
     *
     * @var int
     */
    public $type;
}
