<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Item\CmsItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ManualItem::class)]
final class ManualItemTest extends TestCase
{
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
