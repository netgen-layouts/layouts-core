<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\ValueObject;

class RuleUpdateStruct extends ValueObject
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var int
     */
    public $priority;

    /**
     * @var string
     */
    public $comment;
}
