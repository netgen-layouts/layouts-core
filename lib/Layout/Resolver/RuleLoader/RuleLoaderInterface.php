<?php

namespace Netgen\BlockManager\Layout\Resolver\RuleLoader;

use Netgen\BlockManager\Layout\Resolver\TargetInterface;

interface RuleLoaderInterface
{
    /**
     * Loads the rules based on target.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\TargetInterface $target
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule[]
     */
    public function loadRules(TargetInterface $target);
}
