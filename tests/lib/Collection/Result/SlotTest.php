<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\Collection\Result\Slot;
use PHPUnit\Framework\TestCase;

final class SlotTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Collection\Result\Slot::getName
     * @covers \Netgen\Layouts\Collection\Result\Slot::getObject
     * @covers \Netgen\Layouts\Collection\Result\Slot::getRemoteId
     * @covers \Netgen\Layouts\Collection\Result\Slot::getValue
     * @covers \Netgen\Layouts\Collection\Result\Slot::getValueType
     * @covers \Netgen\Layouts\Collection\Result\Slot::isVisible
     */
    public function testObject(): void
    {
        $value = new Slot();

        self::assertSame(0, $value->getValue());
        self::assertSame(0, $value->getRemoteId());
        self::assertSame('slot', $value->getValueType());
        self::assertSame('(UNKNOWN ITEM)', $value->getName());
        self::assertTrue($value->isVisible());
        self::assertNull($value->getObject());
    }
}
