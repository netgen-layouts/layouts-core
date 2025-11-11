<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullTargetType::class)]
final class NullTargetTypeTest extends TestCase
{
    private NullTargetType $targetType;

    protected function setUp(): void
    {
        $this->targetType = new NullTargetType();
    }

    public function testGetType(): void
    {
        self::assertSame('null', $this->targetType::getType());
    }

    public function testGetConstraints(): void
    {
        self::assertSame([], $this->targetType->getConstraints());
    }
}
