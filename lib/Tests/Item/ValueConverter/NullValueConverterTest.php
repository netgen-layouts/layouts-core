<?php

namespace Netgen\BlockManager\Tests\Item\ValueConverter;

use Netgen\BlockManager\Item\ValueConverter\NullValueConverter;
use stdClass;

class NullValueConverterTest extends \PHPUnit_Framework_TestCase
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
        self::assertTrue($this->valueConverter->supports(null));
        self::assertFalse($this->valueConverter->supports(new stdClass()));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueConverter\NullValueConverter::getValueType
     */
    public function testGetValueType()
    {
        self::assertEquals('null', $this->valueConverter->getValueType(null));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueConverter\NullValueConverter::getId
     */
    public function testGetId()
    {
        self::assertEquals(0, $this->valueConverter->getId(null));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueConverter\NullValueConverter::getName
     */
    public function testGetName()
    {
        self::assertEquals('(INVALID ITEM)', $this->valueConverter->getName(null));
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueConverter\NullValueConverter::getIsVisible
     */
    public function testGetIsVisible()
    {
        self::assertTrue($this->valueConverter->getIsVisible(null));
    }
}
