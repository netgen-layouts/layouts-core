<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\CmsItem;
use PHPUnit\Framework\TestCase;
use stdClass;

final class CmsItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\CmsItem::getName
     * @covers \Netgen\BlockManager\Item\CmsItem::getObject
     * @covers \Netgen\BlockManager\Item\CmsItem::getRemoteId
     * @covers \Netgen\BlockManager\Item\CmsItem::getValue
     * @covers \Netgen\BlockManager\Item\CmsItem::getValueType
     * @covers \Netgen\BlockManager\Item\CmsItem::isVisible
     */
    public function testObject(): void
    {
        $object = new stdClass();

        $value = new CmsItem(
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
