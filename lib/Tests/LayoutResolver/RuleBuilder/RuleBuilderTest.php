<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleBuilder;

use Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilder;
use Netgen\BlockManager\LayoutResolver\Condition;
use Netgen\BlockManager\LayoutResolver\Rule;
use Netgen\BlockManager\LayoutResolver\Target;

class RuleBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilder::buildRules
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

        $target = new Target('target', array('values'));
        $rule1 = new Rule(
            42,
            $target,
            array(
                new Condition(
                    'condition',
                    array(1, 2, 3)
                ),
            )
        );

        $rule2 = new Rule(84, $target);

        $rule3 = new Rule(
            85,
            $target,
            array(
                new Condition(
                    'condition',
                    array()
                ),
            )
        );

        $rules = array($rule1, $rule2, $rule3);

        $ruleBuilder = new RuleBuilder();
        self::assertEquals($rules, $ruleBuilder->buildRules($target, $data));
    }
}
