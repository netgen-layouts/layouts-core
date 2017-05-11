<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Symfony\Component\HttpFoundation\Request;

interface LayoutResolverInterface
{
    /**
     * Resolves the rules based on the provided request.
     *
     * If no request is provided, current request is used.
     *
     * Rules are sorted based on their priorities, descending,
     * meaning the rule with highest priority will be the first one in the list.
     *
     * Rules with same priorities will have undetermined relative positions between each other.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param bool $matchConditions
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function resolveRules(Request $request = null, $matchConditions = true);

    /**
     * Matches the rules based on provided target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetType, $targetValue);

    /**
     * Returns true if the rule matches the provided request.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool If condition type does not exist for one of the conditions
     */
    public function matches(Rule $rule, Request $request);
}
