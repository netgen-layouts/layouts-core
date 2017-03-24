<?php

namespace Netgen\BlockManager\Tests\Layout\Type;

use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Layout\Type\Zone;
use PHPUnit\Framework\TestCase;

class LayoutTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    protected $layoutType;

    public function setUp()
    {
        $this->layoutType = new LayoutType(
            array(
                'identifier' => '4_zones_a',
                'isEnabled' => false,
                'name' => '4 zones A',
                'zones' => array(
                    'left' => new Zone(
                        array(
                            'identifier' => 'left',
                            'name' => 'Left',
                            'allowedBlockDefinitions' => array('title', 'text'),
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
            ),
            $this->layoutType->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\LayoutType::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers()
    {
        $this->assertEquals(array('left'), $this->layoutType->getZoneIdentifiers());
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
        $this->assertFalse($this->layoutType->hasZone('right'));
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
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Zone "right" does not exist in "4_zones_a" layout type.
     */
    public function testGetZoneThrowsInvalidArgumentException()
    {
        $this->layoutType->getZone('right');
    }
}
