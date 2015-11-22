<?php

namespace Netgen\BlockManager\LayoutResolver\RuleBuilder;

interface RuleBuilderInterface
{
    /**
     * Builds the rule objects from the normalized array received from rule handler.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule[]
     */
    public function buildRules(array $data);
}
