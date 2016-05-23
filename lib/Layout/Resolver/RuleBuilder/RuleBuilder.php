<?php

namespace Netgen\BlockManager\Layout\Resolver\RuleBuilder;

use Netgen\BlockManager\Layout\Resolver\Condition;
use Netgen\BlockManager\Layout\Resolver\Rule;
use Netgen\BlockManager\Layout\Resolver\Target;

class RuleBuilder implements RuleBuilderInterface
{
    /**
     * Builds the rule objects from the normalized array received from rule handler.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\Target $target
     * @param array $data
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Rule[]
     */
    public function buildRules(Target $target, array $data)
    {
        $builtRules = array();

        foreach ($data as $rule) {
            $layoutId = $rule['layout_id'];
            $builtConditions = array();

            foreach ($rule['conditions'] as $condition) {
                $builtConditions[] = new Condition(
                    array(
                        'identifier' => $condition['identifier'],
                        'parameters' => is_array($condition['parameters']) ?
                            $condition['parameters'] :
                            array()
                    )
                );
            }

            $builtRules[] = new Rule(
                array(
                    'layoutId' => $layoutId,
                    'target' => $target,
                    'conditions' => $builtConditions,
                )
            );
        }

        return $builtRules;
    }
}
