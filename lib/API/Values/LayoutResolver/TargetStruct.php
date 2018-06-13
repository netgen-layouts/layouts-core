<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\Value;

abstract class TargetStruct extends Value
{
    /**
     * The value of the target.
     *
     * @var int|string
     */
    public $value;
}
