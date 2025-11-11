<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\ConditionType;

use Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullConditionType::class)]
final class NullConditionTypeTest extends TestCase
{
    private NullConditionType $conditionType;

    protected function setUp(): void
    {
        $this->conditionType = new NullConditionType();
    }

    public function testGetType(): void
    {
        self::assertSame('null', $this->conditionType::getType());
    }

    public function testGetConstraints(): void
    {
        self::assertSame([], $this->conditionType->getConstraints());
    }
}
