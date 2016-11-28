<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

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
