<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

interface RuleInterface
{
    /**
     * Returns the layout ID attached to this rule.
     *
     * @return int|string
     */
    public function getLayoutId();

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
