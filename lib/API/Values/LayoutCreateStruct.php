<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\ValueObject;

class LayoutCreateStruct extends ValueObject
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $name;
}
