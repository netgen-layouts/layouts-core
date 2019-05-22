<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\Collection\Result\UnknownItem;
use PHPUnit\Framework\TestCase;

final class UnknownItemTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Collection\Result\UnknownItem::getName
     * @covers \Netgen\Layouts\Collection\Result\UnknownItem::getObject
     * @covers \Netgen\Layouts\Collection\Result\UnknownItem::getRemoteId
     * @covers \Netgen\Layouts\Collection\Result\UnknownItem::getValue
     * @covers \Netgen\Layouts\Collection\Result\UnknownItem::getValueType
     * @covers \Netgen\Layouts\Collection\Result\UnknownItem::isVisible
     */
    public function testObject(): void
    {
        $value = new UnknownItem();

        self::assertSame(0, $value->getValue());
        self::assertSame(0, $value->getRemoteId());
        self::assertSame('unknown', $value->getValueType());
        self::assertSame('(UNKNOWN ITEM)', $value->getName());
        self::assertTrue($value->isVisible());
        self::assertNull($value->getObject());
    }
}
