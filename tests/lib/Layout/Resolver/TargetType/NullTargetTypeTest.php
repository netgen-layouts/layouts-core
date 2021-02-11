<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class NullTargetTypeTest extends TestCase
{
    private NullTargetType $targetType;

    protected function setUp(): void
    {
        $this->targetType = new NullTargetType();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType::getType
     */
    public function testGetType(): void
    {
        self::assertSame('null', $this->targetType::getType());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType::getConstraints
     */
    public function testGetConstraints(): void
    {
        self::assertSame([], $this->targetType->getConstraints());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType::provideValue
     */
    public function testProvideValue(): void
    {
        self::assertNull($this->targetType->provideValue(Request::create('')));
    }
}
