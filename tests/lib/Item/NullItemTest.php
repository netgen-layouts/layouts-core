<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\NullItem;
use PHPUnit\Framework\TestCase;

final class NullItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\NullItem::getValue
     * @covers \Netgen\BlockManager\Item\NullItem::getRemoteId
     * @covers \Netgen\BlockManager\Item\NullItem::getValueType
     * @covers \Netgen\BlockManager\Item\NullItem::getName
     * @covers \Netgen\BlockManager\Item\NullItem::isVisible
     * @covers \Netgen\BlockManager\Item\NullItem::getObject
     */
    public function testObject()
    {
        $value = new NullItem(
            array(
                'value' => 42,
            )
        );

        $this->assertEquals(42, $value->getValue());
        $this->assertEquals(42, $value->getRemoteId());
        $this->assertEquals('null', $value->getValueType());
        $this->assertEquals('(INVALID ITEM)', $value->getName());
        $this->assertTrue($value->isVisible());
        $this->assertNull($value->getObject());
    }
}
