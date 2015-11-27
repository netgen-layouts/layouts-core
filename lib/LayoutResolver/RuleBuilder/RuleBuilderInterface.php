<?php

namespace Netgen\BlockManager\LayoutResolver\RuleBuilder;

use Netgen\BlockManager\LayoutResolver\TargetInterface;

interface RuleBuilderInterface
{
    /**
     * Builds the rule objects from the normalized array received from rule handler.
     *
     * @param \Netgen\BlockManager\LayoutResolver\TargetInterface $target
     * @param array $data
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule[]
     */
    public function buildRules(TargetInterface $target, array $data);
}
