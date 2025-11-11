<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Item;

use Netgen\Layouts\Exception\Item\ItemException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemException::class)]
final class ItemExceptionTest extends TestCase
{
    public function testNoValueType(): void
    {
        $exception = ItemException::noValueType('type');

        self::assertSame(
            'Value type "type" does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoValueLoader(): void
    {
        $exception = ItemException::noValueLoader('type');

        self::assertSame(
            'Value loader for "type" value type does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoValueUrlGenerator(): void
    {
        $exception = ItemException::noValueUrlGenerator('type');

        self::assertSame(
            'Value URL generator for "type" value type does not exist.',
            $exception->getMessage(),
        );
    }

    public function testInvalidUrlType(): void
    {
        $exception = ItemException::invalidUrlType('type', 'unknown');

        self::assertSame(
            '"unknown" URL type is invalid for "type" value type.',
            $exception->getMessage(),
        );
    }

    public function testNoValue(): void
    {
        $exception = ItemException::noValue(42);

        self::assertSame(
            'Value with (remote) ID 42 does not exist.',
            $exception->getMessage(),
        );
    }

    public function testInvalidValue(): void
    {
        $exception = ItemException::invalidValue('type');

        self::assertSame(
            'Item "type" is not valid.',
            $exception->getMessage(),
        );
    }

    public function testCanNotLoadItem(): void
    {
        $exception = ItemException::canNotLoadItem();

        self::assertSame(
            'Item could not be loaded.',
            $exception->getMessage(),
        );
    }
}
