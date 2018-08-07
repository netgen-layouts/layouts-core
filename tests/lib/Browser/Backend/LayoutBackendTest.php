<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Browser\Backend;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Browser\Backend\LayoutBackend;
use Netgen\BlockManager\Browser\Item\Layout\LayoutInterface;
use Netgen\BlockManager\Browser\Item\Layout\RootLocation;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\ContentBrowser\Item\ItemInterface;
use PHPUnit\Framework\TestCase;

final class LayoutBackendTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \Netgen\BlockManager\Browser\Backend\LayoutBackend
     */
    private $backend;

    public function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->backend = new LayoutBackend($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::__construct
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSections
     */
    public function testGetSections(): void
    {
        $this->layoutServiceMock
            ->expects(self::never())
            ->method('loadLayout');

        $locations = $this->backend->getSections();

        self::assertCount(1, $locations);
        self::assertInstanceOf(RootLocation::class, $locations[0]);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::loadLocation
     */
    public function testLoadLocation(): void
    {
        $this->layoutServiceMock
            ->expects(self::never())
            ->method('loadLayout');

        $location = $this->backend->loadLocation(1);

        self::assertInstanceOf(RootLocation::class, $location);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::loadItem
     */
    public function testLoadItem(): void
    {
        $layout = new Layout();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::identicalTo(1))
            ->will(self::returnValue($layout));

        /** @var \Netgen\BlockManager\Browser\Item\Layout\LayoutInterface $item */
        $item = $this->backend->loadItem(1);

        self::assertInstanceOf(ItemInterface::class, $item);
        self::assertInstanceOf(LayoutInterface::class, $item);
        self::assertSame($layout, $item->getLayout());
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::loadItem
     * @expectedException \Netgen\ContentBrowser\Exceptions\NotFoundException
     * @expectedExceptionMessage Item with value "1" not found.
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::identicalTo(1))
            ->will(self::throwException(new NotFoundException('layout', 1)));

        $this->backend->loadItem(1);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubLocations
     */
    public function testGetSubLocations(): void
    {
        $locations = $this->backend->getSubLocations(new RootLocation());

        self::assertSame([], $locations);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubLocationsCount
     */
    public function testGetSubLocationsCount(): void
    {
        $count = $this->backend->getSubLocationsCount(new RootLocation());

        self::assertSame(0, $count);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItems
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubItems
     */
    public function testGetSubItems(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayouts')
            ->with(
                self::identicalTo(false),
                self::identicalTo(0),
                self::identicalTo(25)
            )
            ->will(self::returnValue([new Layout(), new Layout()]));

        $items = $this->backend->getSubItems(new RootLocation());

        self::assertCount(2, $items);
        foreach ($items as $item) {
            self::assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItems
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubItems
     */
    public function testGetSubItemsWithOffsetAndLimit(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayouts')
            ->with(
                self::identicalTo(false),
                self::identicalTo(5),
                self::identicalTo(10)
            )
            ->will(self::returnValue([new Layout(), new Layout()]));

        $items = $this->backend->getSubItems(
            new RootLocation(),
            5,
            10
        );

        self::assertCount(2, $items);
        foreach ($items as $item) {
            self::assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubItemsCount
     */
    public function testGetSubItemsCount(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('getLayoutsCount')
            ->will(self::returnValue(2));

        $count = $this->backend->getSubItemsCount(new RootLocation());

        self::assertSame(2, $count);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::search
     */
    public function testSearch(): void
    {
        $items = $this->backend->search('test');

        self::assertSame([], $items);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::search
     */
    public function testSearchWithOffsetAndLimit(): void
    {
        $items = $this->backend->search('test', 5, 10);

        self::assertSame([], $items);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::searchCount
     */
    public function testSearchCount(): void
    {
        $count = $this->backend->searchCount('test');

        self::assertSame(0, $count);
    }
}
