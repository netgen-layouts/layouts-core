<?php

namespace Netgen\BlockManager\Tests\Item\ValueType;

use Netgen\BlockManager\Item\ValueType\ValueType;
use PHPUnit\Framework\TestCase;

class ValueTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType
     */
    private $valueType;

    public function setUp()
    {
        $this->valueType = new ValueType(
            array(
                'identifier' => 'value',
                'isEnabled' => false,
                'name' => 'Value type',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueType\ValueType::__construct
     * @covers \Netgen\BlockManager\Item\ValueType\ValueType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('value', $this->valueType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueType\ValueType::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertFalse($this->valueType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueType\ValueType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Value type', $this->valueType->getName());
    }
}
