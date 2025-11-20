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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

use function sprintf;

#[CoversClass(LayoutBackend::class)]
final class LayoutBackendTest extends TestCase
{
    private MockObject&LayoutService $layoutServiceMock;

    private LayoutBackend $backend;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->backend = new LayoutBackend(
            $this->layoutServiceMock,
            new Configuration('layout', 'Layout', []),
        );
    }

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

    public function testLoadLocation(): void
    {
        $this->layoutServiceMock
            ->expects(self::never())
            ->method('loadLayout');

        $location = $this->backend->loadLocation(1);

        self::assertSame((new RootLocation())->getLocationId(), $location->getLocationId());
    }

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

    public function testGetSubLocations(): void
    {
        $locations = $this->backend->getSubLocations(new RootLocation());

        self::assertIsArray($locations);
        self::assertEmpty($locations);
    }

    public function testGetSubLocationsCount(): void
    {
        $count = $this->backend->getSubLocationsCount(new RootLocation());

        self::assertSame(0, $count);
    }

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

    public function testGetSubItemsCount(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('getLayoutsCount')
            ->willReturn(2);

        $count = $this->backend->getSubItemsCount(new RootLocation());

        self::assertSame(2, $count);
    }

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

    public function testSearchItems(): void
    {
        $searchResult = $this->backend->searchItems(new SearchQuery('test'));

        self::assertEmpty($searchResult->getResults());
    }

    public function testSearchItemsWithOffsetAndLimit(): void
    {
        $searchResult = $this->backend->searchItems((new SearchQuery('test'))->setOffset(5)->setLimit(10));

        self::assertEmpty($searchResult->getResults());
    }

    public function testSearchItemsCount(): void
    {
        $count = $this->backend->searchItemsCount(new SearchQuery('test'));

        self::assertSame(0, $count);
    }
}
