<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Layout;

use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use PHPUnit\Framework\TestCase;

final class LayoutTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Layout\LayoutTypeException::noLayoutType
     */
    public function testNoLayoutType(): void
    {
        $exception = LayoutTypeException::noLayoutType('type');

        self::assertSame(
            'Layout type with "type" identifier does not exist.',
            $exception->getMessage(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Exception\Layout\LayoutTypeException::noZone
     */
    public function testNoZone(): void
    {
        $exception = LayoutTypeException::noZone('type', 'zone');

        self::assertSame(
            'Zone "zone" does not exist in "type" layout type.',
            $exception->getMessage(),
        );
    }
}
