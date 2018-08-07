<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Layout;

use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use PHPUnit\Framework\TestCase;

final class LayoutTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Layout\LayoutTypeException::noLayoutType
     */
    public function testNoLayoutType(): void
    {
        $exception = LayoutTypeException::noLayoutType('type');

        self::assertSame(
            'Layout type with "type" identifier does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Layout\LayoutTypeException::noZone
     */
    public function testNoZone(): void
    {
        $exception = LayoutTypeException::noZone('type', 'zone');

        self::assertSame(
            'Zone "zone" does not exist in "type" layout type.',
            $exception->getMessage()
        );
    }
}
