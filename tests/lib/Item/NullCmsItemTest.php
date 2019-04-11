<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item;

use Netgen\Layouts\Item\NullCmsItem;
use PHPUnit\Framework\TestCase;

final class NullCmsItemTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Item\NullCmsItem::__construct
     * @covers \Netgen\Layouts\Item\NullCmsItem::getName
     * @covers \Netgen\Layouts\Item\NullCmsItem::getObject
     * @covers \Netgen\Layouts\Item\NullCmsItem::getRemoteId
     * @covers \Netgen\Layouts\Item\NullCmsItem::getValue
     * @covers \Netgen\Layouts\Item\NullCmsItem::getValueType
     * @covers \Netgen\Layouts\Item\NullCmsItem::isVisible
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
