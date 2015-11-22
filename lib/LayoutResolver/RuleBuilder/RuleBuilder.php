<?php

namespace Netgen\BlockManager\LayoutResolver\RuleBuilder;

use Netgen\BlockManager\LayoutResolver\ConditionMatcher\RegistryInterface;
use Netgen\BlockManager\LayoutResolver\Condition;
use Netgen\BlockManager\LayoutResolver\Rule;

class RuleBuilder implements RuleBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\ConditionMatcher\RegistryInterface
     */
    protected $conditionMatcherRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\LayoutResolver\ConditionMatcher\RegistryInterface $conditionMatcherRegistry
     */
    public function __construct(RegistryInterface $conditionMatcherRegistry)
    {
        $this->conditionMatcherRegistry = $conditionMatcherRegistry;
    }

    /**
     * Builds the rule objects from the normalized array received from rule handler.
     *
     * @param array $data
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule[]
     */
    public function buildRules(array $data)
    {
        $builtRules = array();

        foreach ($data as $rule) {
            $layoutId = $rule['layout_id'];
            $builtConditions = array();

            foreach ($rule['conditions'] as $condition) {
                $conditionMatcher = $this->conditionMatcherRegistry->getConditionMatcher(
                    $condition['identifier']
                );

                $builtConditions[] = new Condition(
                    $conditionMatcher,
                    $condition['value_identifier'],
                    $condition['values']
                );
            }

            $builtRules[] = new Rule($layoutId, $builtConditions);
        }

        return $builtRules;
    }
}
