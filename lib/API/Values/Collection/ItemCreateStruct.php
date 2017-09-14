<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\ValueObject;

class ItemCreateStruct extends ValueObject
{
    /**
     * The ID of the value within the item.
     *
     * Required.
     *
     * @var int|string
     */
    public $valueId;

    /**
     * The type of the value within the item.
     *
     * Required.
     *
     * @var string
     */
    public $valueType;

    /**
     * Type of the item. One of Item::TYPE_* constants.
     *
     * Required.
     *
     * @var int
     */
    public $type;
}
