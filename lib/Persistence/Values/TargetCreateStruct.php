<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\ValueObject;

class TargetCreateStruct extends ValueObject
{
    /**
     * @var int|string
     */
    public $ruleId;

    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $identifier;

    /**
     * @var mixed
     */
    public $value;
}
