<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver;

use Netgen\Layouts\Layout\Resolver\ConditionType;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType2;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConditionType::class)]
final class ConditionTypeTest extends TestCase
{
    private ConditionType2 $conditionType;

    protected function setUp(): void
    {
        $this->conditionType = new ConditionType2();
    }

    public function testExport(): void
    {
        self::assertSame('foo', $this->conditionType->export('foo'));
    }

    public function testImport(): void
    {
        self::assertSame('foo', $this->conditionType->import('foo'));
    }
}
