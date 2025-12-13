<?php

declare(strict_types=1);

namespace Netgen\Layouts\Browser\Backend;

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
use Symfony\Component\Uid\Exception\InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

use function sprintf;

final class LayoutBackend implements BackendInterface
{
    public function __construct(
        private LayoutService $layoutService,
        private Configuration $config,
    ) {}

    public function getSections(): iterable
    {
        yield new RootLocation();
    }

    public function loadLocation(int|string $id): RootLocation
    {
        return new RootLocation();
    }

    public function loadItem(int|string $value): Item
    {
        try {
            $layout = $this->layoutService->loadLayout(Uuid::fromString((string) $value));
        } catch (BaseNotFoundException|InvalidArgumentException $e) {
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
     * @return iterable<\Netgen\Layouts\Browser\Item\Layout\Item>
     */
    private function buildItems(LayoutList $layouts): iterable
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
