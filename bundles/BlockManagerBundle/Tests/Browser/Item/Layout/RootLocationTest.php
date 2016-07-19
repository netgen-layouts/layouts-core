<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Browser\Item\Layout;

use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\RootLocation;
use PHPUnit\Framework\TestCase;

class RootLocationTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\EzTags\Location
     */
    protected $location;

    public function setUp()
    {
        $this->location = new RootLocation();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\EzTags\Location::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\EzTags\Location::getId
     */
    public function testGetId()
    {
        self::assertEquals(0, $this->location->getId());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\EzTags\Location::getType
     */
    public function testGetType()
    {
        self::assertEquals('ngbm_layout', $this->location->getType());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\EzTags\Location::getName
     */
    public function testGetName()
    {
        self::assertEquals('All layouts', $this->location->getName());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\EzTags\Location::getParentId
     */
    public function testGetParentId()
    {
        self::assertEquals(null, $this->location->getParentId());
    }
}
