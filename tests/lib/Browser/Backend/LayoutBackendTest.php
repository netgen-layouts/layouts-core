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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

use function sprintf;

#[CoversClass(LayoutBackend::class)]
final class LayoutBackendTest extends TestCase
{
    private Stub&LayoutService $layoutServiceStub;

    private LayoutBackend $backend;

    protected function setUp(): void
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);

        $this->backend = new LayoutBackend(
            $this->layoutServiceStub,
            new Configuration('layout', 'Layout', []),
        );
    }

    public function testGetSections(): void
    {
        $locations = [...$this->backend->getSections()];

        self::assertCount(1, $locations);

        $location = $locations[0];

        self::assertInstanceOf(RootLocation::class, $location);
    }

    public function testLoadLocation(): void
    {
        $location = $this->backend->loadLocation(1);

        self::assertSame(new RootLocation()->locationId, $location->locationId);
    }

    public function testLoadItem(): void
    {
        $uuid = Uuid::v7();
        $layout = new Layout();

        $this->layoutServiceStub
            ->method('loadLayout')
            ->willReturn($layout);

        $item = $this->backend->loadItem($uuid->toString());

        self::assertSame($layout, $item->layout);
    }

    public function testLoadItemThrowsNotFoundException(): void
    {
        $uuid = Uuid::v7();

        $this->expectException(ContentBrowserNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Item with value "%s" not found.', $uuid->toString()));

        $this->layoutServiceStub
            ->method('loadLayout')
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
        $this->layoutServiceStub
            ->method('loadLayouts')
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
        $this->layoutServiceStub
            ->method('loadLayouts')
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
        $configuration = new Configuration('layout', 'Layout', []);
        $configuration->setParameter('include_shared_layouts', 'true');

        $this->backend = new LayoutBackend(
            $this->layoutServiceStub,
            $configuration,
        );

        $this->layoutServiceStub
            ->method('loadAllLayouts')
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
        $this->layoutServiceStub
            ->method('getLayoutsCount')
            ->willReturn(2);

        $count = $this->backend->getSubItemsCount(new RootLocation());

        self::assertSame(2, $count);
    }

    public function testGetSubItemsCountWithSharedLayouts(): void
    {
        $configuration = new Configuration('layout', 'Layout', []);
        $configuration->setParameter('include_shared_layouts', 'true');

        $this->backend = new LayoutBackend(
            $this->layoutServiceStub,
            $configuration,
        );

        $this->layoutServiceStub
            ->method('getAllLayoutsCount')
            ->willReturn(2);

        $count = $this->backend->getSubItemsCount(new RootLocation());

        self::assertSame(2, $count);
    }

    public function testSearchItems(): void
    {
        $searchResult = $this->backend->searchItems(new SearchQuery('test'));

        self::assertEmpty($searchResult->results);
    }

    public function testSearchItemsWithOffsetAndLimit(): void
    {
        $searchQuery = new SearchQuery('test');
        $searchQuery->offset = 5;
        $searchQuery->limit = 10;

        $searchResult = $this->backend->searchItems($searchQuery);

        self::assertEmpty($searchResult->results);
    }

    public function testSearchItemsCount(): void
    {
        $count = $this->backend->searchItemsCount(new SearchQuery('test'));

        self::assertSame(0, $count);
    }
}
