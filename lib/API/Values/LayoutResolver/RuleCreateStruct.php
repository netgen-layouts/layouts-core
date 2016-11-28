<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

class RuleCreateStruct extends ValueObject
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var int
     */
    public $priority = 0;

    /**
     * @var bool
     */
    public $enabled = false;

    /**
     * @var string
     */
    public $comment;
}
