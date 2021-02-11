<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver;

use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType2;
use PHPUnit\Framework\TestCase;

final class ConditionTypeTest extends TestCase
{
    private ConditionType2 $conditionType;

    protected function setUp(): void
    {
        $this->conditionType = new ConditionType2();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType::export
     */
    public function testExport(): void
    {
        self::assertSame('foo', $this->conditionType->export('foo'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType::import
     */
    public function testImport(): void
    {
        self::assertSame('foo', $this->conditionType->import('foo'));
    }
}
