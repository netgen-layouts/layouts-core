<?php

namespace Netgen\BlockManager\LayoutResolver\RuleLoader;

use Netgen\BlockManager\LayoutResolver\TargetInterface;

interface RuleLoaderInterface
{
    /**
     * Loads the rules based on target.
     *
     * @param \Netgen\BlockManager\LayoutResolver\TargetInterface $target
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule[]
     */
    public function loadRules(TargetInterface $target);
}
