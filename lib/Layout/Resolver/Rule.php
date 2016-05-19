<?php

namespace Netgen\BlockManager\Layout\Resolver;

class Rule
{
    /**
     * @var int|string
     */
    protected $layoutId;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Target
     */
    protected $target;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Condition[]
     */
    protected $conditions;

    /**
     * Constructor.
     *
     * @param int|string $layoutId
     * @param \Netgen\BlockManager\Layout\Resolver\Target $target
     * @param \Netgen\BlockManager\Layout\Resolver\Condition[] $conditions
     */
    public function __construct($layoutId, Target $target, array $conditions = array())
    {
        $this->layoutId = $layoutId;
        $this->target = $target;
        $this->conditions = $conditions;
    }

    /**
     * Returns resolved layout ID.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns rule target.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Returns rule conditions.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Condition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}
