<?php

declare(strict_types=1);

namespace Netgen\Layouts\Browser\Backend;

use Generator;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Backend\SearchResult;
use Netgen\ContentBrowser\Backend\SearchResultInterface;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use Netgen\Layouts\Browser\Item\Layout\Item;
use Netgen\Layouts\Browser\Item\Layout\RootLocation;
use Netgen\Layouts\Exception\NotFoundException as BaseNotFoundException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;

use function sprintf;

final class LayoutBackend implements BackendInterface
{
    private LayoutService $layoutService;

    private Configuration $config;

    public function __construct(LayoutService $layoutService, Configuration $config)
    {
        $this->layoutService = $layoutService;
        $this->config = $config;
    }

    public function getSections(): iterable
    {
        yield new RootLocation();
    }

    public function loadLocation($id): RootLocation
    {
        return new RootLocation();
    }

    public function loadItem($value): Item
    {
        try {
            $layout = $this->layoutService->loadLayout(Uuid::fromString((string) $value));
        } catch (BaseNotFoundException|InvalidUuidStringException $e) {
            throw new NotFoundException(
                sprintf('Item with value "%s" not found.', $value),
                0,
                $e,
            );
        }

        return $this->buildItem($layout);
    }

    public function getSubLocations(LocationInterface $location): iterable
    {
        return [];
    }

    public function getSubLocationsCount(LocationInterface $location): int
    {
        return 0;
    }

    public function getSubItems(LocationInterface $location, int $offset = 0, int $limit = 25): iterable
    {
        $layouts = $this->includeSharedLayouts() ?
            $this->layoutService->loadAllLayouts(false, $offset, $limit) :
            $this->layoutService->loadLayouts(false, $offset, $limit);

        return $this->buildItems($layouts);
    }

    public function getSubItemsCount(LocationInterface $location): int
    {
        return $this->includeSharedLayouts() ?
            $this->layoutService->getAllLayoutsCount() :
            $this->layoutService->getLayoutsCount();
    }

    public function searchItems(SearchQuery $searchQuery): SearchResultInterface
    {
        return new SearchResult();
    }

    public function searchItemsCount(SearchQuery $searchQuery): int
    {
        return 0;
    }

    public function search(string $searchText, int $offset = 0, int $limit = 25): iterable
    {
        return [];
    }

    public function searchCount(string $searchText): int
    {
        return 0;
    }

    /**
     * Builds the item from provided layout.
     */
    private function buildItem(Layout $layout): Item
    {
        return new Item($layout);
    }

    /**
     * Builds the items from provided layouts.
     *
     * @return \Generator<\Netgen\Layouts\Browser\Item\Layout\Item>
     */
    private function buildItems(LayoutList $layouts): Generator
    {
        foreach ($layouts as $layout) {
            yield $this->buildItem($layout);
        }
    }

    /**
     * Returns if the backend should include shared layouts or not.
     */
    private function includeSharedLayouts(): bool
    {
        if (!$this->config->hasParameter('include_shared_layouts')) {
            return false;
        }

        return $this->config->getParameter('include_shared_layouts') === 'true';
    }
}
