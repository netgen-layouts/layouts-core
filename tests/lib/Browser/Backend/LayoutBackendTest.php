<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Browser\Backend;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutList;
use Netgen\BlockManager\Browser\Backend\LayoutBackend;
use Netgen\BlockManager\Browser\Item\Layout\LayoutInterface;
use Netgen\BlockManager\Browser\Item\Layout\RootLocation;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\NotFoundException as ContentBrowserNotFoundException;
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

        $this->backend = new LayoutBackend(
            $this->layoutServiceMock,
            new Configuration('ngbm_layout', 'Layout', [])
        );
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
        self::assertContainsOnlyInstancesOf(RootLocation::class, $locations);
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
            ->willReturn($layout);

        $item = $this->backend->loadItem(1);

        self::assertInstanceOf(LayoutInterface::class, $item);
        self::assertSame($layout, $item->getLayout());
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::loadItem
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->expectException(ContentBrowserNotFoundException::class);
        $this->expectExceptionMessage('Item with value "1" not found.');

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::identicalTo(1))
            ->willThrowException(new NotFoundException('layout', 1));

        $this->backend->loadItem(1);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubLocations
     */
    public function testGetSubLocations(): void
    {
        $locations = $this->backend->getSubLocations(new RootLocation());

        self::assertIsArray($locations);
        self::assertEmpty($locations);
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
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::includeSharedLayouts
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
            ->willReturn(new LayoutList([new Layout(), new Layout()]));

        $items = $this->backend->getSubItems(new RootLocation());

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(ItemInterface::class, $items);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::buildItems
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubItems
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::includeSharedLayouts
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
            ->willReturn(new LayoutList([new Layout(), new Layout()]));

        $items = $this->backend->getSubItems(
            new RootLocation(),
            5,
            10
        );

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(ItemInterface::class, $items);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::getSubItemsCount
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::includeSharedLayouts
     */
    public function testGetSubItemsCount(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('getLayoutsCount')
            ->willReturn(2);

        $count = $this->backend->getSubItemsCount(new RootLocation());

        self::assertSame(2, $count);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::search
     */
    public function testSearch(): void
    {
        $items = $this->backend->search('test');

        self::assertIsArray($items);
        self::assertEmpty($items);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Backend\LayoutBackend::search
     */
    public function testSearchWithOffsetAndLimit(): void
    {
        $items = $this->backend->search('test', 5, 10);

        self::assertIsArray($items);
        self::assertEmpty($items);
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
