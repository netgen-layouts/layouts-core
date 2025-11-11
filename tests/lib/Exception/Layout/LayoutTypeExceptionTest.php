<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Layout;

use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LayoutTypeException::class)]
final class LayoutTypeExceptionTest extends TestCase
{
    public function testNoLayoutType(): void
    {
        $exception = LayoutTypeException::noLayoutType('type');

        self::assertSame(
            'Layout type with "type" identifier does not exist.',
            $exception->getMessage(),
        );
    }

    public function testNoZone(): void
    {
        $exception = LayoutTypeException::noZone('type', 'zone');

        self::assertSame(
            'Zone "zone" does not exist in "type" layout type.',
            $exception->getMessage(),
        );
    }
}
