<?php

namespace Netgen\BlockManager\LayoutResolver\RuleLoader;

use Netgen\BlockManager\LayoutResolver\Target;

interface RuleLoaderInterface
{
    /**
     * Loads the rules based on target.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule[]
     */
    public function loadRules(Target $target);
}
