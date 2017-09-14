<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

abstract class TargetStruct extends ValueObject
{
    /**
     * The value of the target.
     *
     * @var int|string
     */
    public $value;
}
