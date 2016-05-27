<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface LayoutResolverInterface
{
    /**
     * Matches the rules based on current conditions.
     *
     * Rules are sorted based on their priorities, descending,
     * meaning the rule with highest priority will be the first one in the list.
     *
     * Rules with same priorities will have undetermined relative positions between each other.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function resolveRules();

    /**
     * Matches the rules based on provided target identifier and value.
     *
     * @param string $targetIdentifier
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetIdentifier, $targetValue);
}
