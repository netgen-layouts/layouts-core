<?php

namespace Netgen\BlockManager\Layout\Resolver\RuleBuilder;

use Netgen\BlockManager\Layout\Resolver\Target;

interface RuleBuilderInterface
{
    /**
     * Builds the rule objects from the normalized array received from rule handler.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Target $target
     * @param array $data
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule[]
     */
    public function buildRules(Target $target, array $data);
}
