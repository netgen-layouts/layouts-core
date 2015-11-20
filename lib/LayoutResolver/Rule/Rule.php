<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

use Netgen\BlockManager\API\Values\Page\Layout;

class Rule implements RuleInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Layout
     */
    protected $layout;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface[]
     */
    protected $targets;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface[] $targets
     */
    public function __construct(Layout $layout, array $targets)
    {
        $this->layout = $layout;
        $this->targets = $targets;
    }

    /**
     * Returns the layout attached to this rule.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout()
    {
        return $this->layout;
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
