<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item;

use Netgen\Layouts\Item\NullCmsItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullCmsItem::class)]
final class NullCmsItemTest extends TestCase
{
    public function testObject(): void
    {
        $value = new NullCmsItem('value');

        self::assertSame('value', $value->valueType);
        self::assertSame('(INVALID ITEM)', $value->name);
    }
}
