<?php

namespace Netgen\BlockManager\LayoutResolver\RuleBuilder;

use Netgen\BlockManager\LayoutResolver\Target;

interface RuleBuilderInterface
{
    /**
     * Builds the rule objects from the normalized array received from rule handler.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Target $target
     * @param array $data
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule[]
     */
    public function buildRules(Target $target, array $data);
}
