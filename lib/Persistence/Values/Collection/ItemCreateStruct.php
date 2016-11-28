<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

class ItemCreateStruct extends ValueObject
{
    /**
     * @var int
     */
    public $position;

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
