<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Item;

use Netgen\BlockManager\Exception\Item\ItemException;
use PHPUnit\Framework\TestCase;

final class ItemExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Item\ItemException::noValueType
     */
    public function testNoValueLoader(): void
    {
        $exception = ItemException::noValueType('type');

        $this->assertSame(
            'Value type "type" does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Item\ItemException::noValue
     */
    public function testNoValue(): void
    {
        $exception = ItemException::noValue(42);

        $this->assertSame(
            'Value with (remote) ID 42 does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Item\ItemException::invalidValue
     */
    public function testInvalidValue(): void
    {
        $exception = ItemException::invalidValue('type');

        $this->assertSame(
            'Item "type" is not valid.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Item\ItemException::canNotLoadItem
     */
    public function testCanNotLoadItem(): void
    {
        $exception = ItemException::canNotLoadItem();

        $this->assertSame(
            'Item could not be loaded.',
            $exception->getMessage()
        );
    }
}
