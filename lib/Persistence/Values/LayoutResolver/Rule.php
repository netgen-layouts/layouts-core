<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Persistence\Values\Value;

class Rule extends Value
{
    /**
     * @var int|string
     */
    public $id;

    /**
     * @var int
     */
    public $status;

    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var bool
     */
    public $enabled;

    /**
     * @var int
     */
    public $priority;

    /**
     * @var string
     */
    public $comment;
}
