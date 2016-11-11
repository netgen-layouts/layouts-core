<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\ValueObject;

class ConditionCreateStruct extends ValueObject
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var mixed
     */
    public $value;
}
