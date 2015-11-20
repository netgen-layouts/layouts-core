<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

class Rule implements RuleInterface
{
    /**
     * @var int|string
     */
    protected $layoutId;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface[]
     */
    protected $targets;

    /**
     * Constructor.
     *
     * @param int|string $layoutId
     * @param \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface[] $targets
     */
    public function __construct($layoutId, array $targets)
    {
        $this->layoutId = $layoutId;
        $this->targets = $targets;
    }

    /**
     * Returns the layout ID attached to this rule.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns the targets from this rule.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface[]
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Returns if any of this rule targets match.
     *
     * @return bool
     */
    public function matches()
    {
        foreach ($this->targets as $target) {
            if (!$target->matches()) {
                continue;
            }

            return true;
        }

        return false;
    }
}
