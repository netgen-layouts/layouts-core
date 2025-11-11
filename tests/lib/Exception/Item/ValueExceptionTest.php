<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Item;

use Netgen\Layouts\Exception\Item\ValueException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValueException::class)]
final class ValueExceptionTest extends TestCase
{
    public function testNoValueLoader(): void
    {
        $exception = ValueException::noValueLoader('type');

        self::assertSame(
            'Value loader for "type" value type does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoValueConverter(): void
    {
        $exception = ValueException::noValueConverter('type');

        self::assertSame(
            'Value converter for "type" type does not exist.',
            $exception->getMessage(),
        );
    }
}
