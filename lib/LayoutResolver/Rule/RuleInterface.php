<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

interface RuleInterface
{
    /**
     * Returns the layout attached to this rule.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout();

    /**
     * Returns the targets from this rule.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface[]
     */
    public function getTargets();

    /**
     * Returns if any of this rule targets match.
     *
     * @return bool
     */
    public function matches();
}
