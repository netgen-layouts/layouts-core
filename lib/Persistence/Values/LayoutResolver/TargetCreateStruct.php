<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

class TargetCreateStruct extends ValueObject
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
