<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use PHPUnit\Framework\TestCase;

final class ConditionTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry
     */
    private $registry;

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
        $this->assertEquals(['type' => $this->conditionType], $this->registry->getConditionTypes());
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

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::getIterator
     */
    public function testGetIterator()
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $conditionTypes = [];
        foreach ($this->registry as $identifier => $conditionType) {
            $conditionTypes[$identifier] = $conditionType;
        }

        $this->assertEquals($this->registry->getConditionTypes(), $conditionTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::count
     */
    public function testCount()
    {
        $this->assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::offsetExists
     */
    public function testOffsetExists()
    {
        $this->assertArrayHasKey('type', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals($this->conditionType, $this->registry['type']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet()
    {
        $this->registry['type'] = $this->conditionType;
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset()
    {
        unset($this->registry['type']);
    }
}
