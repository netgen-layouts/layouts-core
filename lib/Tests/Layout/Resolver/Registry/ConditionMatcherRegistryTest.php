<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\Registry\ConditionMatcherRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionMatcher;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface
     */
    protected $conditionMatcher;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionMatcherRegistryInterface
     */
    protected $registry;

    public function setUp()
    {
        $this->conditionMatcher = new ConditionMatcher();

        $this->registry = new ConditionMatcherRegistry();
        $this->registry->addConditionMatcher($this->conditionMatcher);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionMatcherRegistry::addConditionMatcher
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionMatcherRegistry::getConditionMatchers
     */
    public function testAddConditionMatcher()
    {
        self::assertEquals(array('condition' => $this->conditionMatcher), $this->registry->getConditionMatchers());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionMatcherRegistry::getConditionMatcher
     */
    public function testGetConditionMatcher()
    {
        self::assertEquals($this->conditionMatcher, $this->registry->getConditionMatcher('condition'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionMatcherRegistry::getConditionMatcher
     * @expectedException \RuntimeException
     */
    public function testGetConditionMatcherThrowsRuntimeException()
    {
        $this->registry->getConditionMatcher('other_condition');
    }
}
