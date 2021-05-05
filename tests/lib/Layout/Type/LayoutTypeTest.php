<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Type;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Layout\Type\Zone;
use PHPUnit\Framework\TestCase;

final class LayoutTypeTest extends TestCase
{
    private LayoutType $layoutType;

    private Zone $zone1;

    private Zone $zone2;

    protected function setUp(): void
    {
        $this->zone1 = Zone::fromArray(
            [
                'identifier' => 'left',
                'name' => 'Left',
                'allowedBlockDefinitions' => ['title', 'text'],
            ],
        );

        $this->zone2 = Zone::fromArray(
            [
                'identifier' => 'right',
                'name' => 'Right',
                'allowedBlockDefinitions' => [],
            ],
        );

        $this->layoutType = LayoutType::fromArray(
            [
                'identifier' => '4_zones_a',
                'isEnabled' => false,
                'name' => '4 zones A',
                'icon' => '/icon.svg',
                'zones' => [
                    'left' => $this->zone1,
                    'right' => $this->zone2,
                ],
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('4_zones_a', $this->layoutType->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::isEnabled
     */
    public function testIsEnabled(): void
    {
        self::assertFalse($this->layoutType->isEnabled());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::getName
     */
    public function testGetName(): void
    {
        self::assertSame('4 zones A', $this->layoutType->getName());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::getIcon
     */
    public function testGetIcon(): void
    {
        self::assertSame('/icon.svg', $this->layoutType->getIcon());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::getZones
     */
    public function testGetZones(): void
    {
        self::assertSame(
            [
                'left' => $this->zone1,
                'right' => $this->zone2,
            ],
            $this->layoutType->getZones(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers(): void
    {
        self::assertSame(['left', 'right'], $this->layoutType->getZoneIdentifiers());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::hasZone
     */
    public function testHasZone(): void
    {
        self::assertTrue($this->layoutType->hasZone('left'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::hasZone
     */
    public function testHasZoneReturnsFalse(): void
    {
        self::assertFalse($this->layoutType->hasZone('unknown'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::getZone
     */
    public function testGetZone(): void
    {
        self::assertSame($this->zone1, $this->layoutType->getZone('left'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::getZone
     */
    public function testGetZoneThrowsLayoutTypeException(): void
    {
        $this->expectException(LayoutTypeException::class);
        $this->expectExceptionMessage('Zone "unknown" does not exist in "4_zones_a" layout type.');

        $this->layoutType->getZone('unknown');
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZone(): void
    {
        self::assertTrue($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'title']), 'left'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZoneReturnsFalse(): void
    {
        self::assertFalse($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'other']), 'left'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZoneWithNonExistentZone(): void
    {
        self::assertTrue($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'title']), 'unknown'));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZoneWithAllBlocksAllowed(): void
    {
        self::assertTrue($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'title']), 'right'));
    }
}
