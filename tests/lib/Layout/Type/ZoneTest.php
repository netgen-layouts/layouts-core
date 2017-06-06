<?php

namespace Netgen\BlockManager\Tests\Layout\Type;

use Netgen\BlockManager\Layout\Type\Zone;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\Zone
     */
    protected $zone;

    public function setUp()
    {
        $this->zone = new Zone(
            array(
                'identifier' => 'left',
                'name' => 'Left',
                'allowedBlockDefinitions' => array('title'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\Zone::__construct
     * @covers \Netgen\BlockManager\Layout\Type\Zone::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('left', $this->zone->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\Zone::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Left', $this->zone->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\Zone::getAllowedBlockDefinitions
     */
    public function testGetAllowedBlockDefinitions()
    {
        $this->assertEquals(array('title'), $this->zone->getAllowedBlockDefinitions());
    }
}
