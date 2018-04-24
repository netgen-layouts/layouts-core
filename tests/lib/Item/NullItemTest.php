<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\NullItem;
use PHPUnit\Framework\TestCase;

final class NullItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\NullItem::__construct
     * @covers \Netgen\BlockManager\Item\NullItem::getName
     * @covers \Netgen\BlockManager\Item\NullItem::getObject
     * @covers \Netgen\BlockManager\Item\NullItem::getRemoteId
     * @covers \Netgen\BlockManager\Item\NullItem::getValue
     * @covers \Netgen\BlockManager\Item\NullItem::getValueType
     * @covers \Netgen\BlockManager\Item\NullItem::isVisible
     */
    public function testObject()
    {
        $value = new NullItem('value');

        $this->assertNull($value->getValue());
        $this->assertNull($value->getRemoteId());
        $this->assertEquals('value', $value->getValueType());
        $this->assertEquals('(INVALID ITEM)', $value->getName());
        $this->assertTrue($value->isVisible());
        $this->assertNull($value->getObject());
    }
}
