<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Netgen\BlockManager\Layout\Resolver\TargetBuilder\RegistryInterface as BuilderRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\ConditionMatcher\RegistryInterface as MatcherRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoaderInterface;

class LayoutResolver implements LayoutResolverInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetBuilder\RegistryInterface
     */
    protected $targetBuilderRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\RegistryInterface
     */
    protected $conditionMatcherRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoaderInterface
     */
    protected $ruleLoader;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\TargetBuilder\RegistryInterface $targetBuilderRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\RegistryInterface $conditionMatcherRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\RuleLoader\RuleLoaderInterface $ruleLoader
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
     * @return \Netgen\BlockManager\Layout\Resolver\Rule
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
     * @param \Netgen\BlockManager\Layout\Resolver\TargetInterface $target
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule
     */
    public function resolveLayoutForTarget(TargetInterface $target)
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
     * @param \Netgen\BlockManager\Layout\Resolver\Condition[] $conditions
     *
     * @return bool
     */
    protected function matchConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            $conditionMatcher = $this->conditionMatcherRegistry->getConditionMatcher($condition->identifier);
            if (!$conditionMatcher->matches($condition->parameters)) {
                return false;
            }
        }

        return true;
    }
}
