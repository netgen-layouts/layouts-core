<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\RuleBuilder;

use Netgen\BlockManager\LayoutResolver\Tests\Stubs\ConditionMatcher;
use Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilder;
use Netgen\BlockManager\LayoutResolver\Condition;
use Netgen\BlockManager\LayoutResolver\Rule;
use PHPUnit_Framework_TestCase;

class RuleBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $conditionMatcherRegistryMock;

    public function setUp()
    {
        $this->conditionMatcherRegistryMock = $this->getMock(
            'Netgen\BlockManager\LayoutResolver\ConditionMatcher\RegistryInterface'
        );

        $this->conditionMatcherRegistryMock
            ->expects($this->any())
            ->method('getConditionMatcher')
            ->with($this->equalTo('condition_matcher'))
            ->will($this->returnValue(new ConditionMatcher()));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilder::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilder::buildRules
     */
    public function testBuildRules()
    {
        $data = array(
            array(
                'layout_id' => 42,
                'conditions' => array(
                    array(
                        'matcher' => 'condition_matcher',
                        'value_identifier' => 'identifier',
                        'values' => array(1, 2, 3),
                    ),
                ),
            ),
            array(
                'layout_id' => 84,
                'conditions' => array(),
            ),
        );

        $rule1 = new Rule(
            42,
            array(
                new Condition(
                    new ConditionMatcher(),
                    'identifier',
                    array(1, 2, 3)
                ),
            )
        );

        $rule2 = new Rule(84);

        $rules = array($rule1, $rule2);

        $ruleBuilder = $this->getRuleBuilder();
        self::assertEquals($rules, $ruleBuilder->buildRules($data));
    }

    /**
     * Returns the rule builder under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleBuilder\RuleBuilderInterface
     */
    protected function getRuleBuilder()
    {
        return new RuleBuilder($this->conditionMatcherRegistryMock);
    }
}
