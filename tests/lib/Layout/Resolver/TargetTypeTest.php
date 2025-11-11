<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver;

use Netgen\Layouts\Layout\Resolver\TargetType;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TargetType::class)]
final class TargetTypeTest extends TestCase
{
    private TargetType1 $targetType;

    protected function setUp(): void
    {
        $this->targetType = new TargetType1();
    }

    public function testExport(): void
    {
        self::assertSame('foo', $this->targetType->export('foo'));
    }

    public function testImport(): void
    {
        self::assertSame('foo', $this->targetType->import('foo'));
    }
}
