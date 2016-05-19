<?php

namespace Netgen\BlockManager\Layout\Resolver\RuleLoader;

use Netgen\BlockManager\Layout\Resolver\Target;

interface RuleLoaderInterface
{
    /**
     * Loads the rules based on target.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Target $target
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule[]
     */
    public function loadRules(Target $target);
}
