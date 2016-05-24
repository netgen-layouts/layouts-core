<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\RuleBuilder;

use Netgen\BlockManager\Layout\Resolver\RuleBuilder\RuleBuilder;
use Netgen\BlockManager\Layout\Resolver\Condition;
use Netgen\BlockManager\Layout\Resolver\Rule;
use Netgen\BlockManager\Layout\Resolver\Target;

class RuleBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\RuleBuilder\RuleBuilder::buildRules
     */
    public function testBuildRules()
    {
        $data = array(
            array(
                'layout_id' => 42,
                'conditions' => array(
                    array(
                        'identifier' => 'condition',
                        'parameters' => array(1, 2, 3),
                    ),
                ),
            ),
            array(
                'layout_id' => 84,
                'conditions' => array(),
            ),
            array(
                'layout_id' => 85,
                'conditions' => array(
                    array(
                        'identifier' => 'condition',
                        'parameters' => null,
                    ),
                ),
            ),
        );

        $target = new Target(array('identifier' => 'target', 'values' => array('values')));
        $rule1 = new Rule(
            array(
                'layoutId' => 42,
                'target' => $target,
                'conditions' => array(
                    new Condition(
                        array(
                            'identifier' => 'condition',
                            'parameters' => array(1, 2, 3),
                        )
                    ),
                ),
            )
        );

        $rule2 = new Rule(
            array(
                'layoutId' => 84,
                'target' => $target,
            )
        );

        $rule3 = new Rule(
            array(
                'layoutId' => 85,
                'target' => $target,
                'conditions' => array(
                    new Condition(
                        array(
                            'identifier' => 'condition',
                            'parameters' => array(),
                        )
                    ),
                ),
            )
        );

        $rules = array($rule1, $rule2, $rule3);

        $ruleBuilder = new RuleBuilder();
        self::assertEquals($rules, $ruleBuilder->buildRules($target, $data));
    }
}
