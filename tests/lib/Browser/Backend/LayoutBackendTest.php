<?php

namespace Netgen\BlockManager\Tests\Browser\Backend;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Browser\Backend\LayoutBackend;
use Netgen\BlockManager\Browser\Item\Layout\RootLocation;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\ContentBrowser\Item\ItemInterface;
use PHPUnit\Framework\TestCase;

class LayoutBackendTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \Netgen\BlockManager\Browser\Backend\LayoutBackend
     */
    private $backend;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->backend = new LayoutBackend($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::__construct
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getDefaultSections
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
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::loadLocation
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
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::loadItem
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItem
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
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::loadItem
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with ID 1 not found.
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
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubLocations
     */
    public function testGetSubLocations()
    {
        $locations = $this->backend->getSubLocations(new RootLocation());

        $this->assertEquals(array(), $locations);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubLocationsCount
     */
    public function testGetSubLocationsCount()
    {
        $count = $this->backend->getSubLocationsCount(new RootLocation());

        $this->assertEquals(0, $count);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubItems
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItems
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
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubItems
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItems
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
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubItemsCount
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
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::search
     */
    public function testSearch()
    {
        $items = $this->backend->search('test');

        $this->assertEquals(array(), $items);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::search
     */
    public function testSearchWithOffsetAndLimit()
    {
        $items = $this->backend->search('test', 5, 10);

        $this->assertEquals(array(), $items);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::searchCount
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
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private function getLayout($id = null)
    {
        return new Layout(
            array(
                'id' => $id,
            )
        );
    }
}
