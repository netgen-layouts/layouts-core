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
     * Returns the first valid rule that matches the current conditions.
     *
     * This method will dispatch an event when match is found.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule|null
     */
    public function resolveRule();

    /**
     * Matches the rules based on provided target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetType, $targetValue);
}
