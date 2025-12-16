<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Type;

use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Layout\Type\NullLayoutType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullLayoutType::class)]
final class NullLayoutTypeTest extends TestCase
{
    private NullLayoutType $layoutType;

    protected function setUp(): void
    {
        $this->layoutType = new NullLayoutType('type');
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('type', $this->layoutType->identifier);
    }

    public function testGetName(): void
    {
        self::assertSame('Invalid layout type', $this->layoutType->name);
    }

    public function testGetIcon(): void
    {
        self::assertSame('', $this->layoutType->icon);
    }

    public function testGetZones(): void
    {
        self::assertSame([], $this->layoutType->zones);
    }

    public function testGetZoneIdentifiers(): void
    {
        self::assertSame([], $this->layoutType->zoneIdentifiers);
    }

    public function testGetZone(): void
    {
        $this->expectException(LayoutTypeException::class);
        $this->expectExceptionMessage('Zone "left" does not exist in "type" layout type.');

        $this->layoutType->getZone('left');
    }
}
