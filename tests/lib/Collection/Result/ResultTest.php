<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Item\CmsItem;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\Result::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Result::getItem
     * @covers \Netgen\BlockManager\Collection\Result\Result::getPosition
     * @covers \Netgen\BlockManager\Collection\Result\Result::getSubItem
     */
    public function testObject(): void
    {
        $item1 = CmsItem::fromArray(['value' => 42]);
        $item2 = CmsItem::fromArray(['value' => 43]);

        $result = new Result(0, $item1, $item2);

        $this->assertSame(0, $result->getPosition());
        $this->assertSame($item1, $result->getItem());
        $this->assertSame($item2, $result->getSubItem());
    }
}
