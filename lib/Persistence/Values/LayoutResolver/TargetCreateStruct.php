<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

class TargetCreateStruct extends ValueObject
{
    /**
     * Identifier of the type of the new target.
     *
     * @var string
     */
    public $type;

    /**
     * Value of the new target.
     *
     * @var int|string
     */
    public $value;
}
