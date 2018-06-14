<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Type;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\Zone;
use PHPUnit\Framework\TestCase;

final class LayoutTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    private $layoutType;

    public function setUp(): void
    {
        $this->layoutType = new LayoutType(
            [
                'identifier' => '4_zones_a',
                'isEnabled' => false,
                'name' => '4 zones A',
                'icon' => '/icon.svg',
                'zones' => [
                    'left' => new Zone(
                        [
                            'identifier' => 'left',
                            'name' => 'Left',
                            'allowedBlockDefinitions' => ['title', 'text'],
                        ]
                    ),
                    'right' => new Zone(
                        [
                            'identifier' => 'right',
                            'name' => 'Right',
                            'allowedBlockDefinitions' => null,
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::__construct
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('4_zones_a', $this->layoutType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isEnabled
     */
    public function testIsEnabled(): void
    {
        $this->assertFalse($this->layoutType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getName
     */
    public function testGetName(): void
    {
        $this->assertEquals('4 zones A', $this->layoutType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getIcon
     */
    public function testGetIcon(): void
    {
        $this->assertEquals('/icon.svg', $this->layoutType->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getZones
     */
    public function testGetZones(): void
    {
        $this->assertEquals(
            [
                'left' => new Zone(
                    [
                        'identifier' => 'left',
                        'name' => 'Left',
                        'allowedBlockDefinitions' => ['title', 'text'],
                    ]
                ),
                'right' => new Zone(
                    [
                        'identifier' => 'right',
                        'name' => 'Right',
                        'allowedBlockDefinitions' => null,
                    ]
                ),
            ],
            $this->layoutType->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers(): void
    {
        $this->assertEquals(['left', 'right'], $this->layoutType->getZoneIdentifiers());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::hasZone
     */
    public function testHasZone(): void
    {
        $this->assertTrue($this->layoutType->hasZone('left'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::hasZone
     */
    public function testHasZoneReturnsFalse(): void
    {
        $this->assertFalse($this->layoutType->hasZone('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getZone
     */
    public function testGetZone(): void
    {
        $this->assertEquals(
            new Zone(
                [
                    'identifier' => 'left',
                    'name' => 'Left',
                    'allowedBlockDefinitions' => ['title', 'text'],
                ]
            ),
            $this->layoutType->getZone('left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getZone
     * @expectedException \Netgen\BlockManager\Exception\Layout\LayoutTypeException
     * @expectedExceptionMessage Zone "unknown" does not exist in "4_zones_a" layout type.
     */
    public function testGetZoneThrowsLayoutTypeException(): void
    {
        $this->layoutType->getZone('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZone(): void
    {
        $this->assertTrue($this->layoutType->isBlockAllowedInZone(new BlockDefinition(['identifier' => 'title']), 'left'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZoneReturnsFalse(): void
    {
        $this->assertFalse($this->layoutType->isBlockAllowedInZone(new BlockDefinition(['identifier' => 'other']), 'left'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZoneWithNonExistentZone(): void
    {
        $this->assertTrue($this->layoutType->isBlockAllowedInZone(new BlockDefinition(['identifier' => 'title']), 'unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZoneWithAllBlocksAllowed(): void
    {
        $this->assertTrue($this->layoutType->isBlockAllowedInZone(new BlockDefinition(['identifier' => 'title']), 'right'));
    }
}
