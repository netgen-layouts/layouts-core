<?php

namespace Netgen\BlockManager\Tests\Configuration\LayoutType;

use Netgen\BlockManager\Configuration\LayoutType\Zone;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\LayoutType\Zone
     */
    protected $zone;

    public function setUp()
    {
        $this->zone = new Zone('left', 'Left', array('title'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::__construct
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('left', $this->zone->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Left', $this->zone->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::getAllowedBlockDefinitions
     */
    public function testGetAllowedBlockDefinitions()
    {
        $this->assertEquals(array('title'), $this->zone->getAllowedBlockDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::isBlockDefinitionAllowed
     */
    public function testIsBlockDefinitionAllowed()
    {
        $this->assertTrue($this->zone->isBlockDefinitionAllowed('title'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::isBlockDefinitionAllowed
     */
    public function testIsBlockDefinitionAllowedWithEmptyList()
    {
        $this->zone = new Zone('left', 'Left', array());

        $this->assertTrue($this->zone->isBlockDefinitionAllowed('title'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::isBlockDefinitionAllowed
     */
    public function testIsBlockDefinitionAllowedReturnsFalse()
    {
        $this->assertFalse($this->zone->isBlockDefinitionAllowed('text'));
    }
}
