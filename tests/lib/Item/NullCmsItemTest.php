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

        self::assertNull($value->getValue());
        self::assertNull($value->getRemoteId());
        self::assertSame('value', $value->getValueType());
        self::assertSame('(INVALID ITEM)', $value->getName());
        self::assertTrue($value->isVisible());
        self::assertNull($value->getObject());
    }
}
