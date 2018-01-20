<?php

namespace Netgen\BlockManager\Tests\Layout\Type;

use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\Zone;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use PHPUnit\Framework\TestCase;

final class LayoutTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    private $layoutType;

    public function setUp()
    {
        $this->layoutType = new LayoutType(
            array(
                'identifier' => '4_zones_a',
                'isEnabled' => false,
                'name' => '4 zones A',
                'icon' => '/icon.svg',
                'zones' => array(
                    'left' => new Zone(
                        array(
                            'identifier' => 'left',
                            'name' => 'Left',
                            'allowedBlockDefinitions' => array('title', 'text'),
                        )
                    ),
                    'right' => new Zone(
                        array(
                            'identifier' => 'right',
                            'name' => 'Right',
                            'allowedBlockDefinitions' => null,
                        )
                    ),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::__construct
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('4_zones_a', $this->layoutType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertFalse($this->layoutType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('4 zones A', $this->layoutType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getIcon
     */
    public function testGetIcon()
    {
        $this->assertEquals('/icon.svg', $this->layoutType->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getZones
     */
    public function testGetZones()
    {
        $this->assertEquals(
            array(
                'left' => new Zone(
                    array(
                        'identifier' => 'left',
                        'name' => 'Left',
                        'allowedBlockDefinitions' => array('title', 'text'),
                    )
                ),
                'right' => new Zone(
                    array(
                        'identifier' => 'right',
                        'name' => 'Right',
                        'allowedBlockDefinitions' => null,
                    )
                ),
            ),
            $this->layoutType->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers()
    {
        $this->assertEquals(array('left', 'right'), $this->layoutType->getZoneIdentifiers());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::hasZone
     */
    public function testHasZone()
    {
        $this->assertTrue($this->layoutType->hasZone('left'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::hasZone
     */
    public function testHasZoneReturnsFalse()
    {
        $this->assertFalse($this->layoutType->hasZone('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getZone
     */
    public function testGetZone()
    {
        $this->assertEquals(
            new Zone(
                array(
                    'identifier' => 'left',
                    'name' => 'Left',
                    'allowedBlockDefinitions' => array('title', 'text'),
                )
            ),
            $this->layoutType->getZone('left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getZone
     * @expectedException \Netgen\BlockManager\Exception\Layout\LayoutTypeException
     * @expectedExceptionMessage Zone "unknown" does not exist in "4_zones_a" layout type.
     */
    public function testGetZoneThrowsLayoutTypeException()
    {
        $this->layoutType->getZone('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZone()
    {
        $this->assertTrue($this->layoutType->isBlockAllowedInZone(new BlockDefinition('title'), 'left'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZoneReturnsFalse()
    {
        $this->assertFalse($this->layoutType->isBlockAllowedInZone(new BlockDefinition('other'), 'left'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZoneWithNonExistentZone()
    {
        $this->assertTrue($this->layoutType->isBlockAllowedInZone(new BlockDefinition('title'), 'unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZoneWithAllBlocksAllowed()
    {
        $this->assertTrue($this->layoutType->isBlockAllowedInZone(new BlockDefinition('title'), 'right'));
    }
}
