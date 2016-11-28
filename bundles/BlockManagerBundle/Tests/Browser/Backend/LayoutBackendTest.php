<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Browser\Backend;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\RootLocation;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend;
use PHPUnit\Framework\TestCase;

class LayoutBackendTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend
     */
    protected $backend;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->backend = new LayoutBackend($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::getDefaultSections
     */
    public function testGetDefaultSections()
    {
        $this->layoutServiceMock
            ->expects($this->never())
            ->method('loadLayout');

        $locations = $this->backend->getDefaultSections();

        $this->assertCount(1, $locations);
        $this->assertInstanceOf(RootLocation::class, $locations[0]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::loadLocation
     */
    public function testLoadLocation()
    {
        $this->layoutServiceMock
            ->expects($this->never())
            ->method('loadLayout');

        $location = $this->backend->loadLocation(1);

        $this->assertInstanceOf(RootLocation::class, $location);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::loadItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::buildItem
     */
    public function testLoadItem()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->getLayout(1)));

        $item = $this->backend->loadItem(1);

        $this->assertInstanceOf(ItemInterface::class, $item);
        $this->assertEquals(1, $item->getValue());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::loadItem
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(1))
            ->will($this->throwException(new NotFoundException('layout', 1)));

        $this->backend->loadItem(1);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::getSubLocations
     */
    public function testGetSubLocations()
    {
        $locations = $this->backend->getSubLocations(new RootLocation());

        $this->assertEquals(array(), $locations);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::getSubLocationsCount
     */
    public function testGetSubLocationsCount()
    {
        $count = $this->backend->getSubLocationsCount(new RootLocation());

        $this->assertEquals(0, $count);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::getSubItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::buildItems
     */
    public function testGetSubItems()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayouts')
            ->with(
                $this->equalTo(false),
                $this->equalTo(0),
                $this->equalTo(25)
            )
            ->will($this->returnValue(array($this->getLayout(), $this->getLayout())));

        $items = $this->backend->getSubItems(new RootLocation());

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::getSubItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::buildItems
     */
    public function testGetSubItemsWithOffsetAndLimit()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayouts')
            ->with(
                $this->equalTo(false),
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->will($this->returnValue(array($this->getLayout(), $this->getLayout())));

        $items = $this->backend->getSubItems(
            new RootLocation(),
            5,
            10
        );

        $this->assertCount(2, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::getSubItemsCount
     */
    public function testGetSubItemsCount()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayouts')
            ->will($this->returnValue(array($this->getLayout(), $this->getLayout())));

        $count = $this->backend->getSubItemsCount(new RootLocation());

        $this->assertEquals(2, $count);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::search
     */
    public function testSearch()
    {
        $items = $this->backend->search('test');

        $this->assertEquals(array(), $items);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::search
     */
    public function testSearchWithOffsetAndLimit()
    {
        $items = $this->backend->search('test', 5, 10);

        $this->assertEquals(array(), $items);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Backend\LayoutBackend::searchCount
     */
    public function testSearchCount()
    {
        $count = $this->backend->searchCount('test');

        $this->assertEquals(0, $count);
    }

    /**
     * Returns the layout object used in tests.
     *
     * @param int $id
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    protected function getLayout($id = null)
    {
        return new Layout(
            array(
                'id' => $id,
            )
        );
    }
}
