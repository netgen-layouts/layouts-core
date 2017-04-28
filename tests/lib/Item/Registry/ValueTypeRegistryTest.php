<?php

namespace Netgen\BlockManager\Tests\Item\Registry;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use PHPUnit\Framework\TestCase;

class ValueTypeRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType
     */
    protected $valueType1;

    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType
     */
    protected $valueType2;

    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new ValueTypeRegistry();

        $this->valueType1 = new ValueType(array('isEnabled' => true));
        $this->valueType2 = new ValueType(array('isEnabled' => false));

        $this->registry->addValueType('value1', $this->valueType1);
        $this->registry->addValueType('value2', $this->valueType2);
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::addValueType
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueTypes
     */
    public function testGetValueTypes()
    {
        $this->assertEquals(
            array(
                'value1' => $this->valueType1,
                'value2' => $this->valueType2,
            ),
            $this->registry->getValueTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::addValueType
     * @covers \Netgen\BlockManager\Item\Registry\ValueTypeRegistry::getValueTypes
     */
    public function testGetEnabledValueTypes()
    {
        $this->assertEquals(
            array(
                'value1' => $this->valueType1,
            ),
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
}
