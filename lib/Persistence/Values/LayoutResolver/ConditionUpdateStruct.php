<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Value;

final class ConditionUpdateStruct extends Value
{
    /**
     * Condition value. Can be a scalar or a multidimensional array of scalars.
     *
     * @var mixed
     */
    public $value;
}
