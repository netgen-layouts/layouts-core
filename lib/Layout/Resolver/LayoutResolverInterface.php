<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Symfony\Component\HttpFoundation\Request;

interface LayoutResolverInterface
{
    /**
     * Resolves the rules based on the provided request and returns the first resolved rule
     * or null if no rules were resolved.
     *
     * If no request is provided, current request is used.
     *
     * If $enabledConditions is not an empty array, only the conditions listed in the array will be enabled.
     */
    public function resolveRule(?Request $request = null, array $enabledConditions = []): ?Rule;

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
     * If $enabledConditions is not an empty array, only the conditions listed in the array will be enabled.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $enabledConditions
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function resolveRules(?Request $request = null, array $enabledConditions = []): array;

    /**
     * Returns true if the rule matches the provided request.
     *
     * If $enabledConditions is not an empty array, only the conditions listed in the array will be enabled.
     */
    public function matches(Rule $rule, Request $request, array $enabledConditions = []): bool;
}
