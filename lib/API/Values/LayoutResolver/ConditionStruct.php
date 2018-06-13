<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\Value;

abstract class ConditionStruct extends Value
{
    /**
     * The value of the condition.
     *
     * @var mixed
     */
    public $value;
}
