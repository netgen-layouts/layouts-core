<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

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
    public $priority;

    /**
     * @var bool
     */
    public $enabled;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var int
     */
    public $status;
}
