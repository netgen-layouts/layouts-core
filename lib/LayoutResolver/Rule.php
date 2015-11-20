<?php

namespace Netgen\BlockManager\LayoutResolver;

use Netgen\BlockManager\LayoutResolver\Rule\TargetInterface;

class Rule
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface
     */
    public $target;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\Rule\ConditionInterface[]
     */
    public $conditions = array();

    /**
     * Constructor.
     *
     * @param int|string $layoutId
     * @param \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface $target
     * @param \Netgen\BlockManager\LayoutResolver\Rule\ConditionInterface[] $conditions
     */
    public function __construct($layoutId, TargetInterface $target, array $conditions = array())
    {
        $this->layoutId = $layoutId;
        $this->target = $target;
        $this->conditions = $conditions;
    }
}
