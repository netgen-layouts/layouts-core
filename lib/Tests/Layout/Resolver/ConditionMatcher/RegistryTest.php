<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\ConditionMatcher;

use Netgen\BlockManager\Layout\Resolver\ConditionMatcher\Registry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionMatcher;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface
     */
    protected $conditionMatcher;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\RegistryInterface
     */
    protected $registry;

    public function setUp()
    {
        $this->conditionMatcher = new ConditionMatcher();

        $this->registry = new Registry();
        $this->registry->addConditionMatcher($this->conditionMatcher);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\Registry::addConditionMatcher
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\Registry::getConditionMatchers
     */
    public function testAddConditionMatcher()
    {
        self::assertEquals(array('condition' => $this->conditionMatcher), $this->registry->getConditionMatchers());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\Registry::getConditionMatcher
     */
    public function testGetConditionMatcher()
    {
        self::assertEquals($this->conditionMatcher, $this->registry->getConditionMatcher('condition'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\Registry::getConditionMatcher
     * @expectedException \InvalidArgumentException
     */
    public function testGetConditionMatcherThrowsInvalidArgumentException()
    {
        $this->registry->getConditionMatcher('other_condition');
    }
}
