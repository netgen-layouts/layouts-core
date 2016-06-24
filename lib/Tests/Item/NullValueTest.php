<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\NullValue;
use PHPUnit\Framework\TestCase;

class NullValueTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\NullValue::__construct
     * @covers \Netgen\BlockManager\Item\NullValue::getId
     * @covers \Netgen\BlockManager\Item\NullValue::getValueType
     */
    public function testObject()
    {
        $value = new NullValue(42, 'type');

        self::assertEquals(42, $value->getId());
        self::assertEquals('type', $value->getValueType());
    }
}
