<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Symfony\Component\HttpFoundation\Request;

interface LayoutResolverInterface
{
    /**
     * Resolves the rules based on the provided request and returns the first resolved rule
     * or null if no rules were resolved.
     *
     * DEPRECATED: If no request is provided, current request is used.
     *
     * If $enabledConditions is not an empty array, only the conditions listed in the array will be enabled.
     *
     * @param string[] $enabledConditions
     */
    public function resolveRule(?Request $request = null, array $enabledConditions = []): ?Rule;

    /**
     * Resolves the rules based on the provided request.
     *
     * DEPRECATED: If no request is provided, current request is used.
     *
     * Rules are sorted based on their group placement (tree based) and their descending priorities within a group.
     *
     * Rules with same priorities will have undetermined relative positions between each other.
     *
     * If $enabledConditions is not an empty array, only the conditions listed in the array will be enabled.
     *
     * @param string[] $enabledConditions
     *
     * @return \Netgen\Layouts\API\Values\LayoutResolver\Rule[]
     */
    public function resolveRules(?Request $request = null, array $enabledConditions = []): array;

    /**
     * Returns true if the rule matches the provided request.
     *
     * If $enabledConditions is not an empty array, only the conditions listed in the array will be enabled.
     *
     * @deprecated Will be removed in 2.0. No replacement will be provided.
     *
     * @param string[] $enabledConditions
     */
    public function matches(Rule $rule, Request $request, array $enabledConditions = []): bool;
}
