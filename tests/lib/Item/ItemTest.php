<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\Item::getName
     * @covers \Netgen\BlockManager\Item\Item::getObject
     * @covers \Netgen\BlockManager\Item\Item::getRemoteId
     * @covers \Netgen\BlockManager\Item\Item::getValue
     * @covers \Netgen\BlockManager\Item\Item::getValueType
     * @covers \Netgen\BlockManager\Item\Item::isVisible
     */
    public function testObject(): void
    {
        $object = new stdClass();

        $value = new Item(
            [
                'value' => 42,
                'remoteId' => 'abc',
                'valueType' => 'type',
                'name' => 'Value name',
                'isVisible' => true,
                'object' => $object,
            ]
        );

        $this->assertSame(42, $value->getValue());
        $this->assertSame('abc', $value->getRemoteId());
        $this->assertSame('type', $value->getValueType());
        $this->assertSame('Value name', $value->getName());
        $this->assertTrue($value->isVisible());
        $this->assertSame($object, $value->getObject());
    }
}
