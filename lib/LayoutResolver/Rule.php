<?php

namespace Netgen\BlockManager\LayoutResolver;

class Rule
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var \\Netgen\BlockManager\LayoutResolver\Target
     */
    public $target;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\Condition[]
     */
    public $conditions;

    /**
     * Constructor.
     *
     * @param int|string $layoutId
     * @param \Netgen\BlockManager\LayoutResolver\Target $target
     * @param \Netgen\BlockManager\LayoutResolver\Condition[] $conditions
     */
    public function __construct($layoutId, Target $target, array $conditions = array())
    {
        $this->layoutId = $layoutId;
        $this->target = $target;
        $this->conditions = $conditions;
    }
}
