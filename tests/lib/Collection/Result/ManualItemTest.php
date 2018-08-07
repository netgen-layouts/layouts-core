<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Item\CmsItem;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ManualItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::__construct
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getCollectionItem
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getName
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getObject
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getRemoteId
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getValue
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::getValueType
     * @covers \Netgen\BlockManager\Collection\Result\ManualItem::isVisible
     */
    public function testObject(): void
    {
        $object = new stdClass();

        $collectionItem = Item::fromArray(
            [
                'cmsItem' => CmsItem::fromArray(
                    [
                        'value' => 42,
                        'remoteId' => 'abc',
                        'valueType' => 'type',
                        'name' => 'Value name',
                        'isVisible' => true,
                        'object' => $object,
                    ]
                ),
            ]
        );

        $value = new ManualItem($collectionItem);

        self::assertSame($collectionItem, $value->getCollectionItem());
        self::assertSame(42, $value->getValue());
        self::assertSame('abc', $value->getRemoteId());
        self::assertSame('type', $value->getValueType());
        self::assertSame('Value name', $value->getName());
        self::assertTrue($value->isVisible());
        self::assertSame($object, $value->getObject());
    }
}
