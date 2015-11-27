<?php

namespace Netgen\BlockManager\LayoutResolver;

class Rule
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\TargetInterface
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
     * @param \Netgen\BlockManager\LayoutResolver\TargetInterface $target
     * @param \Netgen\BlockManager\LayoutResolver\Condition[] $conditions
     */
    public function __construct($layoutId, TargetInterface $target, array $conditions = array())
    {
        $this->layoutId = $layoutId;
        $this->target = $target;
        $this->conditions = $conditions;
    }
}
