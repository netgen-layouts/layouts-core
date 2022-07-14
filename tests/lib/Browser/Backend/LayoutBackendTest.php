<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Browser\Backend;

use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\NotFoundException as ContentBrowserNotFoundException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use Netgen\Layouts\Browser\Backend\LayoutBackend;
use Netgen\Layouts\Browser\Item\Layout\RootLocation;
use Netgen\Layouts\Exception\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

use function sprintf;

final class LayoutBackendTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\API\Service\LayoutService
     */
    private MockObject $layoutServiceMock;

    private LayoutBackend $backend;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->backend = new LayoutBackend(
            $this->layoutServiceMock,
            new Configuration('layout', 'Layout', []),
        );
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::__construct
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::getSections
     */
    public function testGetSections(): void
    {
        $this->layoutServiceMock
            ->expects(self::never())
            ->method('loadLayout');

        $locations = [...$this->backend->getSections()];

        self::assertCount(1, $locations);

        $location = $locations[0];

        self::assertInstanceOf(RootLocation::class, $location);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::loadLocation
     */
    public function testLoadLocation(): void
    {
        $this->layoutServiceMock
            ->expects(self::never())
            ->method('loadLayout');

        $location = $this->backend->loadLocation(1);

        self::assertSame((new RootLocation())->getLocationId(), $location->getLocationId());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::loadItem
     */
    public function testLoadItem(): void
    {
        $uuid = Uuid::uuid4();
        $layout = new Layout();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn($layout);

        $item = $this->backend->loadItem($uuid->toString());

        self::assertSame($layout, $item->getLayout());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::loadItem
     */
    public function testLoadItemThrowsNotFoundException(): void
    {
        $uuid = Uuid::uuid4();

        $this->expectException(ContentBrowserNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Item with value "%s" not found.', $uuid->toString()));

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willThrowException(new NotFoundException('layout', $uuid->toString()));

        $this->backend->loadItem($uuid->toString());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::getSubLocations
     */
    public function testGetSubLocations(): void
    {
        $locations = $this->backend->getSubLocations(new RootLocation());

        self::assertIsArray($locations);
        self::assertEmpty($locations);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::getSubLocationsCount
     */
    public function testGetSubLocationsCount(): void
    {
        $count = $this->backend->getSubLocationsCount(new RootLocation());

        self::assertSame(0, $count);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::buildItems
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::getSubItems
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::includeSharedLayouts
     */
    public function testGetSubItems(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayouts')
            ->with(
                self::identicalTo(false),
                self::identicalTo(0),
                self::identicalTo(25),
            )
            ->willReturn(new LayoutList([new Layout(), new Layout()]));

        $items = [];
        foreach ($this->backend->getSubItems(new RootLocation()) as $item) {
            $items[] = $item;
        }

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(ItemInterface::class, $items);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::buildItems
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::getSubItems
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::includeSharedLayouts
     */
    public function testGetSubItemsWithOffsetAndLimit(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayouts')
            ->with(
                self::identicalTo(false),
                self::identicalTo(5),
                self::identicalTo(10),
            )
            ->willReturn(new LayoutList([new Layout(), new Layout()]));

        $items = [];
        foreach ($this->backend->getSubItems(new RootLocation(), 5, 10) as $item) {
            $items[] = $item;
        }

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(ItemInterface::class, $items);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::buildItem
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::buildItems
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::getSubItems
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::includeSharedLayouts
     */
    public function testGetSubItemsWithSharedLayouts(): void
    {
        $this->backend = new LayoutBackend(
            $this->layoutServiceMock,
            new Configuration('layout', 'Layout', [], ['include_shared_layouts' => 'true']),
        );

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadAllLayouts')
            ->with(
                self::identicalTo(false),
                self::identicalTo(0),
                self::identicalTo(25),
            )
            ->willReturn(new LayoutList([new Layout(), new Layout()]));

        $items = [];
        foreach ($this->backend->getSubItems(new RootLocation()) as $item) {
            $items[] = $item;
        }

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(ItemInterface::class, $items);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::getSubItemsCount
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::includeSharedLayouts
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
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::getSubItemsCount
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::includeSharedLayouts
     */
    public function testGetSubItemsCountWithSharedLayouts(): void
    {
        $this->backend = new LayoutBackend(
            $this->layoutServiceMock,
            new Configuration('layout', 'Layout', [], ['include_shared_layouts' => 'true']),
        );

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('getAllLayoutsCount')
            ->willReturn(2);

        $count = $this->backend->getSubItemsCount(new RootLocation());

        self::assertSame(2, $count);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::searchItems
     */
    public function testSearchItems(): void
    {
        $searchResult = $this->backend->searchItems(new SearchQuery('test'));

        self::assertEmpty($searchResult->getResults());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::searchItems
     */
    public function testSearchItemsWithOffsetAndLimit(): void
    {
        $searchResult = $this->backend->searchItems((new SearchQuery('test'))->setOffset(5)->setLimit(10));

        self::assertEmpty($searchResult->getResults());
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::searchItemsCount
     */
    public function testSearchItemsCount(): void
    {
        $count = $this->backend->searchItemsCount(new SearchQuery('test'));

        self::assertSame(0, $count);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::search
     */
    public function testSearch(): void
    {
        $items = $this->backend->search('test');

        self::assertIsArray($items);
        self::assertEmpty($items);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::search
     */
    public function testSearchWithOffsetAndLimit(): void
    {
        $items = $this->backend->search('test', 5, 10);

        self::assertIsArray($items);
        self::assertEmpty($items);
    }

    /**
     * @covers \Netgen\Layouts\Browser\Backend\LayoutBackend::searchCount
     */
    public function testSearchCount(): void
    {
        $count = $this->backend->searchCount('test');

        self::assertSame(0, $count);
    }
}
