<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\ValueObject;

class RuleUpdateStruct extends ValueObject
{
    /**
     * Set to 0 to remove the mapping.
     *
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
