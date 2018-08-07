<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Item;

use Netgen\BlockManager\Exception\Item\ValueException;
use PHPUnit\Framework\TestCase;

final class ValueExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Item\ValueException::noValueLoader
     */
    public function testNoValueLoader(): void
    {
        $exception = ValueException::noValueLoader('type');

        self::assertSame(
            'Value loader for "type" value type does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Item\ValueException::noValueConverter
     */
    public function testNoValueConverter(): void
    {
        $exception = ValueException::noValueConverter('type');

        self::assertSame(
            'Value converter for "type" type does not exist.',
            $exception->getMessage()
        );
    }
}
