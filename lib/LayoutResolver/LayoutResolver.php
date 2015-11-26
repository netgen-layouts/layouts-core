<?php

namespace Netgen\BlockManager\LayoutResolver;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\RegistryInterface as BuilderRegistryInterface;
use Netgen\BlockManager\LayoutResolver\ConditionMatcher\RegistryInterface as MatcherRegistryInterface;
use Netgen\BlockManager\LayoutResolver\RuleLoader\RuleLoaderInterface;

class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\TargetBuilder\RegistryInterface
     */
    protected $targetBuilderRegistry;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\ConditionMatcher\RegistryInterface
     */
    protected $conditionMatcherRegistry;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\RuleLoader\RuleLoaderInterface
     */
    protected $ruleLoader;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\LayoutResolver\TargetBuilder\RegistryInterface $targetBuilderRegistry
     * @param \Netgen\BlockManager\LayoutResolver\ConditionMatcher\RegistryInterface $conditionMatcherRegistry
     * @param \Netgen\BlockManager\LayoutResolver\RuleLoader\RuleLoaderInterface $ruleLoader
     */
    public function __construct(BuilderRegistryInterface $targetBuilderRegistry, MatcherRegistryInterface $conditionMatcherRegistry, RuleLoaderInterface $ruleLoader)
    {
        $this->targetBuilderRegistry = $targetBuilderRegistry;
        $this->conditionMatcherRegistry = $conditionMatcherRegistry;
        $this->ruleLoader = $ruleLoader;
    }

    /**
     * Resolves the layout based on current conditions.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule
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

        return false;
    }

    /**
     * Resolves the layout based on provided target.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule
     */
    public function resolveLayoutForTarget(Target $target)
    {
        $rules = $this->ruleLoader->loadRules($target);
        if (empty($rules)) {
            return false;
        }

        foreach ($rules as $rule) {
            if ($this->matchConditions($rule->conditions)) {
                return $rule;
            }
        }

        return false;
    }

    /**
     * Returns true if all conditions match.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Condition[] $conditions
     *
     * @return bool
     */
    protected function matchConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            $conditionMatcher = $this->conditionMatcherRegistry->getConditionMatcher($condition->identifier);
            if (!$conditionMatcher->matches($condition->valueIdentifier, $condition->values)) {
                return false;
            }
        }

        return true;
    }
}
