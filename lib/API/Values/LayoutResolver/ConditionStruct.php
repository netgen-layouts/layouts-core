<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

abstract class ConditionStruct extends ValueObject
{
    /**
     * The value of the condition.
     *
     * @var mixed
     */
    public $value;
}
