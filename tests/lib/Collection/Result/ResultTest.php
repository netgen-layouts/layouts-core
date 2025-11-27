<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Item\CmsItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Result::class)]
final class ResultTest extends TestCase
{
    public function testObject(): void
    {
        $item1 = CmsItem::fromArray(['value' => 42]);
        $item2 = CmsItem::fromArray(['value' => 43]);

        $result = new Result(0, $item1, $item2);

        self::assertSame(0, $result->position);
        self::assertSame($item1, $result->item);
        self::assertSame($item2, $result->subItem);
        self::assertNull($result->slot);
    }

    public function testObjectWithSlot(): void
    {
        $item1 = CmsItem::fromArray(['value' => 42]);
        $item2 = CmsItem::fromArray(['value' => 43]);
        $slot = Slot::fromArray(['position' => 0]);

        $result = new Result(0, $item1, $item2, $slot);

        self::assertSame(0, $result->position);
        self::assertSame($item1, $result->item);
        self::assertSame($item2, $result->subItem);
        self::assertSame($slot, $result->slot);
    }
}
