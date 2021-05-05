<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Item\CmsItem;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ManualItemTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Collection\Result\ManualItem::__construct
     * @covers \Netgen\Layouts\Collection\Result\ManualItem::getCollectionItem
     * @covers \Netgen\Layouts\Collection\Result\ManualItem::getName
     * @covers \Netgen\Layouts\Collection\Result\ManualItem::getObject
     * @covers \Netgen\Layouts\Collection\Result\ManualItem::getRemoteId
     * @covers \Netgen\Layouts\Collection\Result\ManualItem::getValue
     * @covers \Netgen\Layouts\Collection\Result\ManualItem::getValueType
     * @covers \Netgen\Layouts\Collection\Result\ManualItem::isVisible
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
                    ],
                ),
            ],
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
