<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;

class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface $targetTypeRegistry
     */
    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistryInterface $targetTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
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

        foreach ($this->targetTypeRegistry->getTargetTypes() as $targetType) {
            $targetValue = $targetType->provideValue();
            if ($targetValue === null) {
                continue;
            }

            $matchedRules = array_merge(
                $matchedRules,
                $this->matchRules(
                    $targetType->getType(),
                    $targetValue
                )
            );
        }

        usort(
            $matchedRules,
            function (Rule $a, Rule $b) {
                return $b->getPriority() - $a->getPriority();
            }
        );

        return $matchedRules;
    }

    /**
     * Returns the first valid rule that matches the current conditions.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule|null
     */
    public function resolveRule()
    {
        foreach ($this->resolveRules() as $rule) {
            if (!$rule->getLayout() instanceof Layout) {
                continue;
            }

            return $rule;
        }

        return null;
    }

    /**
     * Matches the rules based on provided target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetType, $targetValue)
    {
        $rules = $this->layoutResolverService->matchRules($targetType, $targetValue);

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
     * @throws \RuntimeException If condition type does not exist for one of the conditions
     *
     * @return bool
     */
    protected function matchConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            $conditionType = $condition->getConditionType();
            if (!$conditionType->matches($condition->getValue())) {
                return false;
            }
        }

        return true;
    }
}
