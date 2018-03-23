<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Value;

final class ConditionCreateStruct extends Value
{
    /**
     * Identifier of the condition type.
     *
     * @var string
     */
    public $type;

    /**
     * Condition value. Can be a scalar or a multidimensional array of scalars.
     *
     * @var mixed
     */
    public $value;
}
