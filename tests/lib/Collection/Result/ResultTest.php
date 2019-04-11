<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Item\CmsItem;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Collection\Result\Result::__construct
     * @covers \Netgen\Layouts\Collection\Result\Result::getItem
     * @covers \Netgen\Layouts\Collection\Result\Result::getPosition
     * @covers \Netgen\Layouts\Collection\Result\Result::getSubItem
     */
    public function testObject(): void
    {
        $item1 = CmsItem::fromArray(['value' => 42]);
        $item2 = CmsItem::fromArray(['value' => 43]);

        $result = new Result(0, $item1, $item2);

        self::assertSame(0, $result->getPosition());
        self::assertSame($item1, $result->getItem());
        self::assertSame($item2, $result->getSubItem());
    }
}
