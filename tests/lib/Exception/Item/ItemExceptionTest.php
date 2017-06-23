<?php

namespace Netgen\BlockManager\Tests\Exception\Item;

use Netgen\BlockManager\Exception\Item\ItemException;
use PHPUnit\Framework\TestCase;

class ItemExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Item\ItemException::noValueType
     */
    public function testNoValueLoader()
    {
        $exception = ItemException::noValueType('type');

        $this->assertEquals(
            'Value type "type" does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Item\ItemException::noValue
     */
    public function testNoValue()
    {
        $exception = ItemException::noValue(42);

        $this->assertEquals(
            'Value with ID 42 does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Item\ItemException::invalidValue
     */
    public function testInvalidValue()
    {
        $exception = ItemException::invalidValue('type');

        $this->assertEquals(
            'Item "type" is not valid.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Item\ItemException::canNotLoadItem
     */
    public function testCanNotLoadItem()
    {
        $exception = ItemException::canNotLoadItem();

        $this->assertEquals(
            'Item could not be loaded.',
            $exception->getMessage()
        );
    }
}
