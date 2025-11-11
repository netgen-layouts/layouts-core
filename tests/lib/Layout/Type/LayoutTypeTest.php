<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Type;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Layout\Type\Zone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LayoutType::class)]
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

    public function testGetIdentifier(): void
    {
        self::assertSame('4_zones_a', $this->layoutType->getIdentifier());
    }

    public function testIsEnabled(): void
    {
        self::assertFalse($this->layoutType->isEnabled());
    }

    public function testGetName(): void
    {
        self::assertSame('4 zones A', $this->layoutType->getName());
    }

    public function testGetIcon(): void
    {
        self::assertSame('/icon.svg', $this->layoutType->getIcon());
    }

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

    public function testGetZoneIdentifiers(): void
    {
        self::assertSame(['left', 'right'], $this->layoutType->getZoneIdentifiers());
    }

    public function testHasZone(): void
    {
        self::assertTrue($this->layoutType->hasZone('left'));
    }

    public function testHasZoneReturnsFalse(): void
    {
        self::assertFalse($this->layoutType->hasZone('unknown'));
    }

    public function testGetZone(): void
    {
        self::assertSame($this->zone1, $this->layoutType->getZone('left'));
    }

    public function testGetZoneThrowsLayoutTypeException(): void
    {
        $this->expectException(LayoutTypeException::class);
        $this->expectExceptionMessage('Zone "unknown" does not exist in "4_zones_a" layout type.');

        $this->layoutType->getZone('unknown');
    }

    public function testIsBlockAllowedInZone(): void
    {
        self::assertTrue($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'title']), 'left'));
    }

    public function testIsBlockAllowedInZoneReturnsFalse(): void
    {
        self::assertFalse($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'other']), 'left'));
    }

    public function testIsBlockAllowedInZoneWithNonExistentZone(): void
    {
        self::assertTrue($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'title']), 'unknown'));
    }

    public function testIsBlockAllowedInZoneWithAllBlocksAllowed(): void
    {
        self::assertTrue($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'title']), 'right'));
    }
}
