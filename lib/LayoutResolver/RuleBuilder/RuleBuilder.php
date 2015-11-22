<?php

namespace Netgen\BlockManager\LayoutResolver\RuleBuilder;


use Netgen\BlockManager\LayoutResolver\Condition;
use Netgen\BlockManager\LayoutResolver\Rule;

class RuleBuilder implements RuleBuilderInterface
{
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
                $builtConditions[] = new Condition(
                    $condition['identifier'],
                    $condition['value_identifier'],
                    $condition['values']
                );
            }

            $builtRules[] = new Rule($layoutId, $builtConditions);
        }

        return $builtRules;
    }
}
