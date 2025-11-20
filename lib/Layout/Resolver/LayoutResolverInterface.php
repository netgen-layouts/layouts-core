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
     * If $enabledConditions is not an empty array, only the conditions listed in the array will be enabled.
     *
     * @param string[] $enabledConditions
     */
    public function resolveRule(Request $request, array $enabledConditions = []): ?Rule;

    /**
     * Resolves the rules based on the provided request.
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
    public function resolveRules(Request $request, array $enabledConditions = []): array;
}
