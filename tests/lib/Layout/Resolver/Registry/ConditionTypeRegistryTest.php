<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use PHPUnit\Framework\TestCase;

class ConditionTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    protected $conditionType;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ConditionTypeRegistry();

        $this->conditionType = new ConditionType('type');
        $this->registry->addConditionType($this->conditionType);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::addConditionType
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionTypes
     */
    public function testGetConditionTypes()
    {
        $this->assertEquals(array('type' => $this->conditionType), $this->registry->getConditionTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionType
     */
    public function testGetConditionType()
    {
        $this->assertEquals($this->conditionType, $this->registry->getConditionType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getConditionType
     * @expectedException \Netgen\BlockManager\Exception\Layout\ConditionTypeException
     * @expectedExceptionMessage Condition type "other_type" does not exist.
     */
    public function testGetConditionTypeThrowsConditionTypeException()
    {
        $this->registry->getConditionType('other_type');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::hasConditionType
     */
    public function testHasConditionType()
    {
        $this->assertTrue($this->registry->hasConditionType('type'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::hasConditionType
     */
    public function testHasConditionTypeWithNoConditionType()
    {
        $this->assertFalse($this->registry->hasConditionType('other_type'));
    }
}
