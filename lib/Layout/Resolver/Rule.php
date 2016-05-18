<?php

namespace Netgen\BlockManager\Layout\Resolver;

class Rule
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetInterface
     */
    public $target;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Condition[]
     */
    public $conditions;

    /**
     * Constructor.
     *
     * @param int|string $layoutId
     * @param \Netgen\BlockManager\Layout\Resolver\TargetInterface $target
     * @param \Netgen\BlockManager\Layout\Resolver\Condition[] $conditions
     */
    public function __construct($layoutId, TargetInterface $target, array $conditions = array())
    {
        $this->layoutId = $layoutId;
        $this->target = $target;
        $this->conditions = $conditions;
    }
}
