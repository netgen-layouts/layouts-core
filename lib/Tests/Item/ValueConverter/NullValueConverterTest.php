<?php

namespace Netgen\BlockManager\Tests\Item\ValueConverter;

use Netgen\BlockManager\Item\NullValue;
use Netgen\BlockManager\Item\ValueConverter\NullValueConverter;
use stdClass;
use PHPUnit\Framework\TestCase;

class NullValueConverterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ValueConverter\NullValueConverter
     */
    protected $valueConverter;

    public function setUp()
    {
        $this->valueConverter = new NullValueConverter();
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueConverter\NullValueConverter::supports
     */
    public function testSupports()
    {
        $this->assertTrue($this->valueConverter->supports(new NullValue(42, 'value')));
        $this->assertFalse($this->valueConverter->supports(new stdClass()));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueConverter\NullValueConverter::getValueType
     */
    public function testGetValueType()
    {
        $this->assertEquals('value', $this->valueConverter->getValueType(new NullValue(42, 'value')));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueConverter\NullValueConverter::getId
     */
    public function testGetId()
    {
        $this->assertEquals(42, $this->valueConverter->getId(new NullValue(42, 'value')));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueConverter\NullValueConverter::getName
     */
    public function testGetName()
    {
        $this->assertEquals('(INVALID ITEM)', $this->valueConverter->getName(new NullValue(42, 'value')));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueConverter\NullValueConverter::getIsVisible
     */
    public function testGetIsVisible()
    {
        $this->assertTrue($this->valueConverter->getIsVisible(new NullValue(42, 'value')));
    }
}
