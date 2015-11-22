<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\ConditionMatcher;

use Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry;
use Netgen\BlockManager\LayoutResolver\Tests\Stubs\ConditionMatcher;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry::addConditionMatcher
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry::getConditionMatchers
     */
    public function testAddConditionMatcher()
    {
        $registry = new Registry();

        $conditionMatcher = new ConditionMatcher();
        $registry->addConditionMatcher($conditionMatcher);

        self::assertEquals(array('condition_matcher' => $conditionMatcher), $registry->getConditionMatchers());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry::getConditionMatcher
     */
    public function testGetConditionMatcher()
    {
        $registry = new Registry();

        $conditionMatcher = new ConditionMatcher();
        $registry->addConditionMatcher($conditionMatcher);

        self::assertEquals($conditionMatcher, $registry->getConditionMatcher('condition_matcher'));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry::getConditionMatcher
     * @expectedException \InvalidArgumentException
     */
    public function testGetConditionMatcherThrowsInvalidArgumentException()
    {
        $registry = new Registry();

        $conditionMatcher = new ConditionMatcher();
        $registry->addConditionMatcher($conditionMatcher);

        $registry->getConditionMatcher('other_condition_matcher');
    }
}
