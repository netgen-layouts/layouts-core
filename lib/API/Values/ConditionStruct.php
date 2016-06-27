<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\ValueObject;

abstract class ConditionStruct extends ValueObject
{
    /**
     * @var mixed
     */
    public $value;
}
