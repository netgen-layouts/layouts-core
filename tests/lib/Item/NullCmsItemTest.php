<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\NullCmsItem;
use PHPUnit\Framework\TestCase;

final class NullCmsItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\NullCmsItem::__construct
     * @covers \Netgen\BlockManager\Item\NullCmsItem::getName
     * @covers \Netgen\BlockManager\Item\NullCmsItem::getObject
     * @covers \Netgen\BlockManager\Item\NullCmsItem::getRemoteId
     * @covers \Netgen\BlockManager\Item\NullCmsItem::getValue
     * @covers \Netgen\BlockManager\Item\NullCmsItem::getValueType
     * @covers \Netgen\BlockManager\Item\NullCmsItem::isVisible
     */
    public function testObject(): void
    {
        $value = new NullCmsItem('value');

        $this->assertNull($value->getValue());
        $this->assertNull($value->getRemoteId());
        $this->assertSame('value', $value->getValueType());
        $this->assertSame('(INVALID ITEM)', $value->getName());
        $this->assertTrue($value->isVisible());
        $this->assertNull($value->getObject());
    }
}
