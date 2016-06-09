<?php

namespace Netgen\BlockManager\Tests\Configuration\LayoutType;

use Netgen\BlockManager\Configuration\LayoutType\Zone;

class ZoneTest extends \PHPUnit\Framework\TestCase
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
        self::assertEquals('left', $this->zone->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::getName
     */
    public function testGetName()
    {
        self::assertEquals('Left', $this->zone->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::getAllowedBlockDefinitions
     */
    public function testGetAllowedBlockDefinitions()
    {
        self::assertEquals(array('title'), $this->zone->getAllowedBlockDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::isBlockDefinitionAllowed
     */
    public function testIsBlockDefinitionAllowed()
    {
        self::assertTrue($this->zone->isBlockDefinitionAllowed('title'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::isBlockDefinitionAllowed
     */
    public function testIsBlockDefinitionAllowedWithEmptyList()
    {
        $this->zone = new Zone('left', 'Left', array());

        self::assertTrue($this->zone->isBlockDefinitionAllowed('title'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\LayoutType\Zone::isBlockDefinitionAllowed
     */
    public function testIsBlockDefinitionAllowedReturnsFalse()
    {
        self::assertFalse($this->zone->isBlockDefinitionAllowed('paragraph'));
    }
}
