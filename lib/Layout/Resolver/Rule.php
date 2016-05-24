<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\ValueObject;

class Rule extends ValueObject
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
    protected $conditions = array();

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var bool
     */
    protected $priority;

    /**
     * @var string
     */
    protected $comment;

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

    /**
     * Returns if the rule is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns the rule priority.
     *
     * @return bool
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Returns the rule comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
