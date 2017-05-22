<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\ValueObject;

class ItemCreateStruct extends ValueObject
{
    /**
     * The ID of the value within the item.
     *
     * @var int|string
     */
    public $valueId;

    /**
     * The type of the value within the item.
     *
     * @var string
     */
    public $valueType;

    /**
     * Type of the item. One of Item::TYPE_* constants.
     *
     * @var int
     */
    public $type;
}
