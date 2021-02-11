<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver;

use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use PHPUnit\Framework\TestCase;

final class TargetTypeTest extends TestCase
{
    private TargetType1 $targetType;

    protected function setUp(): void
    {
        $this->targetType = new TargetType1();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetType::export
     */
    public function testExport(): void
    {
        self::assertSame('foo', $this->targetType->export('foo'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetType::import
     */
    public function testImport(): void
    {
        self::assertSame('foo', $this->targetType->import('foo'));
    }
}
