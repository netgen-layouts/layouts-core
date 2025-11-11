<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\Collection\Result\UnknownItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnknownItem::class)]
final class UnknownItemTest extends TestCase
{
    public function testObject(): void
    {
        $value = new UnknownItem();

        self::assertSame(0, $value->getValue());
        self::assertSame(0, $value->getRemoteId());
        self::assertSame('unknown', $value->getValueType());
        self::assertSame('(UNKNOWN ITEM)', $value->getName());
    }
}
