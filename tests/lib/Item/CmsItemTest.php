<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item;

use Netgen\Layouts\Item\CmsItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(CmsItem::class)]
final class CmsItemTest extends TestCase
{
    public function testObject(): void
    {
        $object = new stdClass();

        $value = CmsItem::fromArray(
            [
                'value' => 42,
                'remoteId' => 'abc',
                'valueType' => 'type',
                'name' => 'Value name',
                'isVisible' => true,
                'object' => $object,
            ],
        );

        self::assertSame(42, $value->getValue());
        self::assertSame('abc', $value->getRemoteId());
        self::assertSame('type', $value->getValueType());
        self::assertSame('Value name', $value->getName());
        self::assertTrue($value->isVisible());
        self::assertSame($object, $value->getObject());
    }
}
