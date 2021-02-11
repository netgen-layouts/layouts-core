<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Type;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Layout\Type\NullLayoutType;
use PHPUnit\Framework\TestCase;

final class NullLayoutTypeTest extends TestCase
{
    private NullLayoutType $layoutType;

    protected function setUp(): void
    {
        $this->layoutType = new NullLayoutType('type');
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::__construct
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('type', $this->layoutType->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::isEnabled
     */
    public function testIsEnabled(): void
    {
        self::assertTrue($this->layoutType->isEnabled());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Invalid layout type', $this->layoutType->getName());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::getIcon
     */
    public function testGetIcon(): void
    {
        self::assertSame('', $this->layoutType->getIcon());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::getZones
     */
    public function testGetZones(): void
    {
        self::assertSame([], $this->layoutType->getZones());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers(): void
    {
        self::assertSame([], $this->layoutType->getZoneIdentifiers());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::hasZone
     */
    public function testHasZone(): void
    {
        self::assertFalse($this->layoutType->hasZone('left'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::getZone
     */
    public function testGetZone(): void
    {
        $this->expectException(LayoutTypeException::class);
        $this->expectExceptionMessage('Zone "left" does not exist in "type" layout type.');

        $this->layoutType->getZone('left');
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\NullLayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZone(): void
    {
        self::assertTrue($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'title']), 'left'));
    }
}
