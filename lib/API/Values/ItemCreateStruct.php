<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\ValueObject;

class ItemCreateStruct extends ValueObject
{
    /**
     * @var int|string
     */
    public $valueId;

    /**
     * @var string
     */
    public $valueType;

    /**
     * @var int
     */
    public $type;
}
