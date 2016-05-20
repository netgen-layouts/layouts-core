<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\Layout\Resolver\Registry\TargetBuilderRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionMatcherRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoaderInterface;

class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetBuilderRegistryInterface
     */
    protected $targetBuilderRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionMatcherRegistryInterface
     */
    protected $conditionMatcherRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoaderInterface
     */
    protected $ruleLoader;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetBuilderRegistryInterface $targetBuilderRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\ConditionMatcherRegistryInterface $conditionMatcherRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoaderInterface $ruleLoader
     */
    public function __construct(
        TargetBuilderRegistryInterface $targetBuilderRegistry,
        ConditionMatcherRegistryInterface $conditionMatcherRegistry,
        RuleLoaderInterface $ruleLoader
    ) {
        $this->targetBuilderRegistry = $targetBuilderRegistry;
        $this->conditionMatcherRegistry = $conditionMatcherRegistry;
        $this->ruleLoader = $ruleLoader;
    }

    /**
     * Resolves the layout based on current conditions.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule|null
     */
    public function resolveLayout()
    {
        foreach ($this->targetBuilderRegistry->getTargetBuilders() as $targetBuilder) {
            $target = $targetBuilder->buildTarget();
            if (!$target instanceof Target) {
                continue;
            }

            $rule = $this->resolveLayoutForTarget($target);
            if ($rule instanceof Rule) {
                return $rule;
            }
        }

        return;
    }

    /**
     * Resolves the layout based on provided target.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Target $target
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule|null
     */
    public function resolveLayoutForTarget(Target $target)
    {
        $rules = $this->ruleLoader->loadRules($target);
        if (empty($rules)) {
            return;
        }

        foreach ($rules as $rule) {
            if ($this->matchConditions($rule->getConditions())) {
                return $rule;
            }
        }

        return;
    }

    /**
     * Returns true if all conditions match.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Condition[] $conditions
     *
     * @return bool
     */
    protected function matchConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            $conditionMatcher = $this->conditionMatcherRegistry->getConditionMatcher($condition->getIdentifier());
            if (!$conditionMatcher->matches($condition->getParameters())) {
                return false;
            }
        }

        return true;
    }
}
