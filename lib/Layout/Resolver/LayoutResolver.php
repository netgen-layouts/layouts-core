<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use RuntimeException;

class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetValueProviderInterface[]
     */
    protected $targetValueProviders = array();

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionMatcherInterface[]
     */
    protected $conditionMatchers = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\Layout\Resolver\TargetValueProviderInterface[] $targetValueProviders
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionMatcherInterface[] $conditionMatchers
     */
    public function __construct(
        LayoutResolverService $layoutResolverService,
        array $targetValueProviders = array(),
        array $conditionMatchers = array()
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetValueProviders = $targetValueProviders;
        $this->conditionMatchers = $conditionMatchers;
    }

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
    public function resolveRules()
    {
        $matchedRules = array();

        foreach ($this->targetValueProviders as $targetIdentifier => $targetValueProvider) {
            if (!$targetValueProvider instanceof TargetValueProviderInterface) {
                throw new RuntimeException(
                    sprintf(
                        "Target value provider for '%s' target must implement TargetValueProviderInterface.",
                        $targetIdentifier
                    )
                );
            }

            $targetValue = $targetValueProvider->provideValue();
            if ($targetValue === null) {
                continue;
            }

            $matchedRules = array_merge(
                $matchedRules,
                $this->matchRules(
                    $targetIdentifier,
                    $targetValue
                )
            );
        }

        usort(
            $matchedRules,
            function (Rule $a, Rule $b) {
                if ($a->getPriority() === $b->getPriority()) {
                    return 0;
                }

                return ($a->getPriority() > $b->getPriority()) ? -1 : 1;
            }
        );

        return $matchedRules;
    }

    /**
     * Matches the rules based on provided target identifier and value.
     *
     * @param string $targetIdentifier
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetIdentifier, $targetValue)
    {
        $rules = $this->layoutResolverService->matchRules($targetIdentifier, $targetValue);

        if (empty($rules)) {
            return array();
        }

        $matchedRules = array();

        foreach ($rules as $rule) {
            if ($rule->isEnabled() && $this->matchConditions($rule->getConditions())) {
                $matchedRules[] = $rule;
            }
        }

        return $matchedRules;
    }

    /**
     * Returns true if all conditions match.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition[] $conditions
     *
     * @throws \RuntimeException If condition matcher does not exist for one of the conditions
     *
     * @return bool
     */
    protected function matchConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            if (!isset($this->conditionMatchers[$condition->getIdentifier()])) {
                throw new RuntimeException(
                    sprintf(
                        "Condition matcher for '%s' condition type does not exist.",
                        $condition->getIdentifier()
                    )
                );
            }

            $conditionMatcher = $this->conditionMatchers[$condition->getIdentifier()];
            if (!$conditionMatcher instanceof ConditionMatcherInterface) {
                throw new RuntimeException(
                    sprintf(
                        "Condition matcher for '%s' condition type must implement ConditionMatcherInterface.",
                        $condition->getIdentifier()
                    )
                );
            }

            if (!$conditionMatcher->matches($condition->getValue())) {
                return false;
            }
        }

        return true;
    }
}
