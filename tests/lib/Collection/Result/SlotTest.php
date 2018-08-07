<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\Slot;
use PHPUnit\Framework\TestCase;

final class SlotTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\Slot::getName
     * @covers \Netgen\BlockManager\Collection\Result\Slot::getObject
     * @covers \Netgen\BlockManager\Collection\Result\Slot::getRemoteId
     * @covers \Netgen\BlockManager\Collection\Result\Slot::getValue
     * @covers \Netgen\BlockManager\Collection\Result\Slot::getValueType
     * @covers \Netgen\BlockManager\Collection\Result\Slot::isVisible
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
