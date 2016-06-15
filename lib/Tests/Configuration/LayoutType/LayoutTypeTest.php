<?php

namespace Netgen\BlockManager\Tests\Configuration\LayoutType;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone;
use PHPUnit\Framework\TestCase;

class LayoutTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    protected $layoutType;

    public function setUp()
    {
        $this->layoutType = new LayoutType(
            '3_zones_a',
            true,
            '3 zones A',
            array(
                'left' => new Zone(
                    'left',
                    'Left',
                    array('title', 'text')
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::__construct
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('3_zones_a', $this->layoutType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::isEnabled
     */
    public function testGetIsEnabled()
    {
        self::assertEquals(true, $this->layoutType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::getName
     */
    public function testGetName()
    {
        self::assertEquals('3 zones A', $this->layoutType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::getZones
     */
    public function testGetZones()
    {
        self::assertEquals(
            array(
                'left' => new Zone(
                    'left',
                    'Left',
                    array('title', 'text')
                ),
            ),
            $this->layoutType->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers()
    {
        self::assertEquals(array('left'), $this->layoutType->getZoneIdentifiers());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::hasZone
     */
    public function testHasZone()
    {
        self::assertTrue($this->layoutType->hasZone('left'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::hasZone
     */
    public function testHasZoneReturnsFalse()
    {
        self::assertFalse($this->layoutType->hasZone('right'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::getZone
     */
    public function testGetZone()
    {
        self::assertEquals(
            new Zone(
                'left',
                'Left',
                array('title', 'text')
            ),
            $this->layoutType->getZone('left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::getZone
     * @expectedException \InvalidArgumentException
     */
    public function testGetZoneThrowsInvalidArgumentException()
    {
        $this->layoutType->getZone('right');
    }
}
