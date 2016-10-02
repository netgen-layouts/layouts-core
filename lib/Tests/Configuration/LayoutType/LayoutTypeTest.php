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
            '4_zones_a',
            '4 zones A',
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
        $this->assertEquals('4_zones_a', $this->layoutType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('4 zones A', $this->layoutType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::getZones
     */
    public function testGetZones()
    {
        $this->assertEquals(
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
        $this->assertEquals(array('left'), $this->layoutType->getZoneIdentifiers());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::hasZone
     */
    public function testHasZone()
    {
        $this->assertTrue($this->layoutType->hasZone('left'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::hasZone
     */
    public function testHasZoneReturnsFalse()
    {
        $this->assertFalse($this->layoutType->hasZone('right'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\LayoutType::getZone
     */
    public function testGetZone()
    {
        $this->assertEquals(
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
