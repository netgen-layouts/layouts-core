<?php

namespace Netgen\BlockManager\Tests\Item\Registry;

use ArrayIterator;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use PHPUnit\Framework\TestCase;

final class ValueTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType
     */
    private $valueType1;

    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType
     */
    private $valueType2;

    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new ValueTypeRegistry();

        $this->valueType1 = new ValueType(['isEnabled' => true]);
        $this->valueType2 = new ValueType(['isEnabled' => false]);

        $this->registry->addValueType('value1', $this->valueType1);
        $this->registry->addValueType('value2', $this->valueType2);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::addValueType
     */
    public function testAddValueType()
    {
        $this->registry->addValueType('test', $this->valueType1);

        $this->assertTrue($this->registry->hasValueType('test'));
        $this->assertEquals($this->valueType1, $this->registry->getValueType('test'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueTypes
     */
    public function testGetValueTypes()
    {
        $this->assertEquals(
            [
                'value1' => $this->valueType1,
                'value2' => $this->valueType2,
            ],
            $this->registry->getValueTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueTypes
     */
    public function testGetEnabledValueTypes()
    {
        $this->assertEquals(
            [
                'value1' => $this->valueType1,
            ],
            $this->registry->getValueTypes(true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueType
     */
    public function testGetValueType()
    {
        $this->assertEquals($this->valueType1, $this->registry->getValueType('value1'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueType
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Value type "other_value" does not exist.
     */
    public function testGetValueTypeThrowsInvalidArgumentException()
    {
        $this->registry->getValueType('other_value');
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::hasValueType
     */
    public function testHasValueType()
    {
        $this->assertTrue($this->registry->hasValueType('value1'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::hasValueType
     */
    public function testHasValueTypeWithNoValueType()
    {
        $this->assertFalse($this->registry->hasValueType('other_value'));
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getIterator
     */
    public function testGetIterator()
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $valueTypes = [];
        foreach ($this->registry as $identifier => $valueType) {
            $valueTypes[$identifier] = $valueType;
        }

        $this->assertEquals($this->registry->getValueTypes(), $valueTypes);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::count
     */
    public function testCount()
    {
        $this->assertCount(2, $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::offsetExists
     */
    public function testOffsetExists()
    {
        $this->assertArrayHasKey('value1', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals($this->valueType1, $this->registry['value1']);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::offsetSet
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet()
    {
        $this->registry['value1'] = $this->valueType1;
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset()
    {
        unset($this->registry['value1']);
    }
}
