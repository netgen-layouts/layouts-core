<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\ConditionMatcher;

use Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry;
use Netgen\BlockManager\Tests\LayoutResolver\Stubs\ConditionMatcher;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface
     */
    protected $conditionMatcher;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\ConditionMatcher\RegistryInterface
     */
    protected $registry;

    public function setUp()
    {
        $this->conditionMatcher = new ConditionMatcher();

        $this->registry = new Registry();
        $this->registry->addConditionMatcher($this->conditionMatcher);
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry::addConditionMatcher
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry::getConditionMatchers
     */
    public function testAddConditionMatcher()
    {
        self::assertEquals(array('condition' => $this->conditionMatcher), $this->registry->getConditionMatchers());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry::getConditionMatcher
     */
    public function testGetConditionMatcher()
    {
        self::assertEquals($this->conditionMatcher, $this->registry->getConditionMatcher('condition'));
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\ConditionMatcher\Registry::getConditionMatcher
     * @expectedException \InvalidArgumentException
     */
    public function testGetConditionMatcherThrowsInvalidArgumentException()
    {
        $this->registry->getConditionMatcher('other_condition');
    }
}
